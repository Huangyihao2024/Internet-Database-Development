<?php
/**
 * 知行学习团队：frontend/controllers/SiteController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace frontend\controllers;
use Yii;
use common\models\{Team,MemberProfile,Announcement,RegistrationForm};
class SiteController extends \common\controllers\AuthController {
 public function actions() { return array_merge(parent::actions(),['captcha'=>['class'=>\yii\captcha\CaptchaAction::class,'fontFile'=>PHP_OS_FAMILY==='Windows' ? getenv('WINDIR').'/Fonts/arial.ttf' : '@yii/captcha/SpicyRice.ttf']]); }
 public function actionIndex() { Yii::beginProfile('team.home','team.profile');$output=$this->render('index',['team'=>Team::find()->one(),'members'=>MemberProfile::find()->orderBy('id')->all(),'announcements'=>Announcement::published()->orderBy(['published_at'=>SORT_DESC,'id'=>SORT_DESC])->limit(5)->all()]);Yii::endProfile('team.home','team.profile');return $output; }
 public function actionSignup() {
  if (!Yii::$app->user->isGuest) return $this->goHome();
  $model=new RegistrationForm();
  if ($model->load(Yii::$app->request->post()) && ($u=$model->register())) { Yii::$app->user->login($u,0);Yii::$app->session->setFlash('success','注册成功，可在个人资料中完善介绍。');return $this->redirect(['member/edit']); }
  $model->password='';return $this->render('@common/views/signup',['model'=>$model]);
 }
 public function actionMe() {
  Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
  if (Yii::$app->user->isGuest) return ['authenticated'=>false];
  $u=Yii::$app->user->identity;return ['authenticated'=>true,'id'=>(int)$u->id,'username'=>$u->username,'role'=>$u->role];
 }
}
