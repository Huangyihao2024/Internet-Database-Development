<?php
/** 小组积分与射手榜。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{Tournament,NationalTeam,TournamentGroup};
use common\services\WorldcupStatistics;
class StandingController extends \yii\web\Controller {
    /** 展示数据库实时计算的积分和射手统计。 */
    public function actionIndex(): string {\common\components\QueryInput::check([],['tournament_id']);$tournaments=Tournament::find()->orderBy(['year'=>SORT_DESC])->all();$id=(int)(Yii::$app->request->get('tournament_id')?:($tournaments[0]->id??0));return $this->render('index',['tournaments'=>$tournaments,'selected'=>$id,'standings'=>$id?WorldcupStatistics::standings($id):[],'scorers'=>$id?WorldcupStatistics::scorers($id):[],'teams'=>NationalTeam::find()->indexBy('id')->all(),'groups'=>TournamentGroup::find()->indexBy('id')->all()]);}
}
