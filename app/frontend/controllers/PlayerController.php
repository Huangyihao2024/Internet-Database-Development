<?php
/** 球员查询。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{Player,NationalTeam};
class PlayerController extends \yii\web\Controller {
    /** 按姓名与球队查询球员。 */
    public function actionIndex(): string {$q=Player::find()->with('team');if (Yii::$app->request->get('team_id'))$q->andWhere(['nationality_team_id'=>(int)Yii::$app->request->get('team_id')]);if (Yii::$app->request->get('q'))$q->andWhere(['like','name',mb_substr(Yii::$app->request->get('q'),0,80)]);$pages=new \yii\data\Pagination(['totalCount'=>(clone $q)->count(),'pageSize'=>24]);return $this->render('index',['players'=>$q->orderBy('name')->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages,'teams'=>NationalTeam::find()->orderBy('name')->all()]);}
    /** 展示球员所属球队和正式进球数。 */
    public function actionView(int $id): string {$player=Player::find()->with(['team','events.match'])->where(['id'=>$id])->one();if (!$player)throw new \yii\web\NotFoundHttpException('球员不存在');return $this->render('view',['player'=>$player]);}
}
