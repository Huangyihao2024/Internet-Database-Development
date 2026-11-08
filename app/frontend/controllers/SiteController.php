<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace frontend\controllers;
use Yii;
use common\models\{FanPost,Announcement,RegistrationForm};
class SiteController extends \common\controllers\AuthController {
 public function actions(){return array_merge(parent::actions(),['captcha'=>['class'=>\yii\captcha\CaptchaAction::class,'fontFile'=>PHP_OS_FAMILY==='Windows'?getenv('WINDIR').'/Fonts/arial.ttf':'@yii/captcha/SpicyRice.ttf']]);}
 public function actionIndex(){Yii::beginProfile('worldcup.home','team.profile');$result=$this->render('index',['posts'=>FanPost::find()->with('author')->where(['status'=>'published'])->orderBy(['id'=>SORT_DESC])->limit(5)->all(),'announcements'=>Announcement::published()->orderBy(['id'=>SORT_DESC])->limit(3)->all()]);Yii::endProfile('worldcup.home','team.profile');return $result;}
 public function actionSignup(){
  if(!Yii::$app->user->isGuest)return $this->goHome();$model=new RegistrationForm();
  if($model->load(Yii::$app->request->post())&&($u=$model->register())){Yii::$app->user->login($u,0);Yii::$app->session->setFlash('success','欢迎加入绿茵视界。');return $this->redirect(['community/index']);}
  $model->password='';return $this->render('@common/views/signup',['model'=>$model]);
 }
 public function actionMe(){Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;if(Yii::$app->user->isGuest)return ['authenticated'=>false];$u=Yii::$app->user->identity;return ['authenticated'=>true,'id'=>(int)$u->id,'username'=>$u->username,'role'=>$u->role];}
}
