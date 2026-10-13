<?php
/** 国家队查询。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{NationalTeam,FootballMatch};
class TeamController extends \yii\web\Controller {
    /** 分页检索国家队。 */
    public function actionIndex(): string {$q=NationalTeam::find();if (Yii::$app->request->get('q')) $q->andWhere(['like','name',mb_substr(Yii::$app->request->get('q'),0,80)]);$pages=new \yii\data\Pagination(['totalCount'=>(clone $q)->count(),'pageSize'=>16]);return $this->render('index',['teams'=>$q->orderBy('name')->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages]);}
    /** 展示球员与比赛记录。 */
    public function actionView(int $id): string {$team=NationalTeam::find()->with('players')->where(['id'=>$id])->one();if (!$team) throw new \yii\web\NotFoundHttpException('球队不存在');$matches=FootballMatch::find()->with(['homeTeam','awayTeam'])->where(['or',['home_team_id'=>$id],['away_team_id'=>$id]])->orderBy('kicks_off_at')->all();return $this->render('view',['team'=>$team,'matches'=>$matches]);}
}
