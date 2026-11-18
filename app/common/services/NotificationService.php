<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace common\services;
use Yii;
class NotificationService {
 public static function notify($email,$subject,$text){
  // 本课程默认仅文件投递，不连接真实SMTP或发送外部邮件。
  Yii::$app->mailer->useFileTransport=!(Yii::$app->params['allowExternalEmail']??false);
  return Yii::$app->mailer->compose()->setFrom('noreply@example.invalid')->setTo($email)->setSubject($subject)->setTextBody($text)->send();
 }
}
