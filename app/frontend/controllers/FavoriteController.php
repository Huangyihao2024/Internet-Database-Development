<?php
/** 登录用户的球队与比赛收藏。负责人：王乐之（2411302）；AI辅助开发。 */
namespace frontend\controllers;
use Yii;
use common\models\{TeamFavorite,MatchFavorite,NationalTeam,FootballMatch};
class FavoriteController extends \yii\web\Controller {
    /** 限制写操作为 POST 并要求登录身份。 */
    public function behaviors(): array {return ['access'=>['class'=>\yii\filters\AccessControl::class,'rules'=>[['allow'=>true,'roles'=>['@']]]],'verbs'=>['class'=>\yii\filters\VerbFilter::class,'actions'=>['toggle'=>['POST']]]];}
    /** 显示当前用户收藏。 */
    public function actionIndex(): string {return $this->render('index',['teams'=>TeamFavorite::find()->with('team')->where(['user_id'=>Yii::$app->user->id])->all(),'matches'=>MatchFavorite::find()->with(['match.homeTeam','match.awayTeam'])->where(['user_id'=>Yii::$app->user->id])->all()]);}
    /** 基于唯一键幂等收藏或取消。 */
    public function actionToggle(string $type,int $id): \yii\web\Response {if (!in_array($type,['team','match'],true))throw new \yii\web\BadRequestHttpException('收藏类型无效');$class=$type==='team'?TeamFavorite::class:MatchFavorite::class;$target=$type==='team'?NationalTeam::class:FootballMatch::class;$key=$type.'_id';if (!$target::findOne($id))throw new \yii\web\NotFoundHttpException('对象不存在');$where=['user_id'=>Yii::$app->user->id,$key=>$id];$existing=$class::findOne($where);if ($existing)$existing->delete();else { $m=new $class();$m->setAttributes($where,false);$m->save(false); }return $this->redirect(Yii::$app->request->referrer?:['index']);}
}
