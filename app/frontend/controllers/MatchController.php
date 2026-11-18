<?php
/** 比赛列表与详情。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{FootballMatch,Tournament,Stage,NationalTeam};
class MatchController extends \yii\web\Controller {
    /** 支持届次、日期、球队、阶段、关键词、排序及分页。 */
    public function actionIndex(): string {\common\components\QueryInput::check(['q', 'date', 'sort'],['tournament_id', 'stage_id', 'team_id']);
        $r=Yii::$app->request;$q=FootballMatch::find()->alias('m')->with(['homeTeam','awayTeam','stage','stadium']);
        foreach (['tournament_id','stage_id'] as $key) if ($r->get($key)!==null && $r->get($key)!=='') $q->andWhere(['m.'.$key=>(int)$r->get($key)]);
        if ($r->get('team_id')) $q->andWhere(['or',['m.home_team_id'=>(int)$r->get('team_id')],['m.away_team_id'=>(int)$r->get('team_id')]]);
        if ($r->get('date')) { $date=$r->get('date');if (!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date) || !checkdate((int)substr($date,5,2),(int)substr($date,8,2),(int)substr($date,0,4))) throw new \yii\web\BadRequestHttpException('日期格式错误');$start=(new \DateTimeImmutable($date.' 00:00:00',new \DateTimeZone('Asia/Shanghai')))->setTimezone(new \DateTimeZone('UTC'));$end=$start->modify('+1 day');$q->andWhere(['>=','m.kicks_off_at',$start->format('Y-m-d H:i:s')])->andWhere(['<','m.kicks_off_at',$end->format('Y-m-d H:i:s')]); }
        if ($r->get('q')) {$q->joinWith(['homeTeam h','awayTeam a']);$q->andWhere(['or',['like','h.name',mb_substr($r->get('q'),0,80)],['like','a.name',mb_substr($r->get('q'),0,80)]]);}
        $sort=$r->get('sort')==='old'?SORT_ASC:SORT_DESC;$pages=new \yii\data\Pagination(['totalCount'=>(clone $q)->count(),'pageSize'=>12]);
        return $this->render('index',['matches'=>$q->orderBy(['m.kicks_off_at'=>$sort,'m.id'=>$sort])->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages,'stages'=>Stage::find()->all(),'teams'=>NationalTeam::find()->orderBy('name')->all(),'tournaments'=>Tournament::find()->orderBy(['year'=>SORT_DESC])->all()]);
    }
    /** 展示比分、事件、球场及收藏入口。 */
    public function actionView(int $id): string {
        $match=FootballMatch::find()->with(['homeTeam','awayTeam','stage','stadium','events.player','events.team'])->where(['id'=>$id])->one();
        if (!$match) throw new \yii\web\NotFoundHttpException('比赛不存在');
        return $this->render('view',['match'=>$match]);
    }
}
