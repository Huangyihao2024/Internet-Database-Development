<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace backend\controllers;
use Yii;
use common\models\GuestbookMessage;
class ModerationController extends \common\controllers\AdminController {
 public function actionIndex(){return $this->render('index',['provider'=>new \yii\data\ActiveDataProvider(['query'=>GuestbookMessage::find()->with('author')->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>15]])]);}
 public function actionUpdate(int $id){
  $m=GuestbookMessage::findOne($id);if(!$m)throw new \yii\web\NotFoundHttpException('留言不存在');
  if(Yii::$app->request->isPost){
   $status=Yii::$app->request->post('status');$reply=Yii::$app->request->post('reply_content','');
   if(!is_string($reply)||mb_strlen($reply)>5000||!in_array($status,['pending','published','rejected','archived'],true))throw new \yii\web\BadRequestHttpException('状态或回复无效');
   $m->status=$status;$m->reply_content=$reply;$m->replied_at=$reply!==''?date('Y-m-d H:i:s'):null;$m->save(false);
   $sent=\common\services\NotificationService::notify($m->author->email,'留言处理通知','你的留言处理状态：'.$status.($reply!==''?"\n管理员回复：".$reply:''));
   Yii::$app->session->setFlash($sent?'success':'warning',$sent?'审核已保存，通知已写入本机邮件目录。':'审核已保存；通知生成失败，请检查日志。');return $this->redirect(['index']);
  }
  return $this->render('update',['model'=>$m]);
 }
}
