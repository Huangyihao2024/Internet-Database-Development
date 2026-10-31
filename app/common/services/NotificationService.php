<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace common\services;
use Yii;
use common\models\{User,Notification};
class NotificationService {
 public static function notifyUser(int $uid,string $subject,string $content):bool {
  $user=User::findOne($uid);if(!$user)return false;
  $n=new Notification();$n->user_id=$uid;$n->subject=mb_substr($subject,0,255);$n->content=$content;$n->channel='file';$n->status='pending';$n->save(false);
  try{
   Yii::$app->mailer->useFileTransport=!(Yii::$app->params['allowExternalEmail']??false);
   $ok=Yii::$app->mailer->compose()->setFrom(Yii::$app->params['senderEmail']??'noreply@example.invalid')->setTo($user->email)->setSubject($subject)->setTextBody($content)->send();
   $n->channel=Yii::$app->mailer->useFileTransport?'file':'smtp';$n->status=$ok?'sent':'failed';$n->save(false);return $ok;
  }catch(\Throwable $e){$n->status='failed';$n->save(false);Yii::warning(['event'=>'notification_failed','id'=>$n->id],'team.mail');return false;}
 }
}
