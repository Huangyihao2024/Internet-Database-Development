<?php
/**
 * 绿茵数据团队：frontend/controllers/ProfileController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace frontend\controllers;
use Yii;
use common\models\{MemberProfile,DeveloperTeam};
class ProfileController extends \yii\web\Controller {
 public function actionIndex() { return $this->render('@frontend/views/member/index',['members'=>MemberProfile::find()->orderBy('id')->all()]); }
 public function actionView(int $id) { $m=MemberProfile::findOne($id);if (!$m) throw new \yii\web\NotFoundHttpException('成员不存在');return $this->render('@frontend/views/member/view',['model'=>$m]); }
 public function actionEdit() {
  if (Yii::$app->user->isGuest) return Yii::$app->user->loginRequired();
  $uid=Yii::$app->user->id;$m=MemberProfile::findOne(['user_id'=>$uid]);
  if (!$m) { $m=new MemberProfile();$m->user_id=$uid;$m->team_id=DeveloperTeam::find()->select('id')->scalar();$m->display_name=Yii::$app->user->identity->username; }
  if ($m->load(Yii::$app->request->post()) && $m->save()) { Yii::$app->session->setFlash('success','个人介绍已保存。');return $this->redirect(['view','id'=>$m->id]); }
  return $this->render('@frontend/views/member/edit',['model'=>$m]);
 }
}
