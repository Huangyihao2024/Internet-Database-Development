<?php
/**
 * 知行学习团队：frontend/controllers/SiteController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace frontend\controllers;
use Yii;
use common\models\{Team,MemberProfile,Announcement,RegistrationForm};
class SiteController extends \common\controllers\AuthController {
 public function actionIndex() { return $this->render('index'); }
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
