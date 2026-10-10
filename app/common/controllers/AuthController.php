<?php
/**
 * 绿茵数据团队：common/controllers/AuthController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\controllers;
use Yii;
use common\models\LoginForm;
class AuthController extends \yii\web\Controller {
 public function actions() { return ['error'=>['class'=>\yii\web\ErrorAction::class]]; }
 public function behaviors() { return ['verbs'=>['class'=>\yii\filters\VerbFilter::class,'actions'=>['logout'=>['POST']]]]; }
 public function actionLogin() {
  $model=new LoginForm();$model->rememberMe=false;
  $key='login-'.hash('sha256',Yii::$app->id.Yii::$app->request->userIP);
  $attempts=(int)Yii::$app->cache->get($key);
  if (Yii::$app->request->isPost) {
   if ($attempts>=10) throw new \yii\web\TooManyRequestsHttpException('尝试次数过多，请一分钟后重试。');
   if ($model->load(Yii::$app->request->post()) && $model->validate()) {
    $u=\common\models\User::findByUsername($model->username);
    if (Yii::$app->id==='app-backend' && !$u->isAdmin) {
     Yii::$app->cache->set($key,$attempts+1,60);throw new \yii\web\ForbiddenHttpException('仅管理员可登录后台；普通成员请从前台登录。');
    }
    Yii::$app->user->login($u,0);\common\models\AuditLog::record('login','user',$u->id);Yii::info(['event'=>'login','user_id'=>$u->id],'team.auth');Yii::$app->cache->delete($key);return $this->goHome();
   }
   Yii::$app->cache->set($key,$attempts+1,60);
  }
  $model->password='';return $this->render('@common/views/login',['model'=>$model]);
 }
 public function actionLogout() { Yii::$app->user->logout();return $this->goHome(); }
}
