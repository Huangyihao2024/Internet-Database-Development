<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace common\models;
class Notification extends \yii\db\ActiveRecord {
 public static function tableName(){return 'wc_sys_notification';}
 public function getRecipient(){return $this->hasOne(User::class,['id'=>'user_id']);}
}
