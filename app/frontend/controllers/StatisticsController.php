<?php
/** 赛事动态图表与 JSON 数据。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{Tournament,Stage,NationalTeam};
use common\services\WorldcupStatistics;
class StatisticsController extends \yii\web\Controller {
    /** 提供四图筛选页面。 */
    public function actionIndex(): string {return $this->render('index',['tournaments'=>Tournament::find()->orderBy(['year'=>SORT_DESC])->all(),'stages'=>Stage::find()->all(),'teams'=>NationalTeam::find()->orderBy('name')->all()]);}
    /** 返回每次筛选从 MySQL 聚合的新数据。 */
    public function actionData(): array {Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;$r=Yii::$app->request;\common\components\QueryInput::check([],['tournament_id', 'stage_id', 'team_id']);$t=(int)$r->get('tournament_id');if (!$t || !Tournament::findOne($t))throw new \yii\web\BadRequestHttpException('请选择有效届次');$stage=$r->get('stage_id')?(int)$r->get('stage_id'):null;$team=$r->get('team_id')?(int)$r->get('team_id'):null;if ($stage && !Stage::find()->where(['id'=>$stage,'tournament_id'=>$t])->exists())throw new \yii\web\BadRequestHttpException('阶段无效');if ($team && !NationalTeam::findOne($team))throw new \yii\web\BadRequestHttpException('球队无效');return WorldcupStatistics::charts($t,$stage,$team);}
}
