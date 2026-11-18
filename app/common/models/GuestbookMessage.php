<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace common\models;
class GuestbookMessage extends \yii\db\ActiveRecord {
 public static function tableName(){return 'guestbook_message';}
 public function rules(){return [[['content'],'required'],['content','trim'],['content','string','min'=>2,'max'=>3000]];}
 public function attributeLabels(){return ['content'=>'留言内容'];}
 public function getAuthor(){return $this->hasOne(User::class,['id'=>'user_id']);}
}
