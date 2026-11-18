<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
/** 留言回复字段兼容迁移；保留已有留言和15张业务表。 */
namespace console\migrations;
class m261118_020000_guestbook_reply extends \yii\db\Migration {
 public function safeUp(){
  $t=$this->db->schema->getTableSchema('guestbook_message',true);
  if(!isset($t->columns['reply_content']))$this->addColumn('guestbook_message','reply_content',$this->text());
  if(!isset($t->columns['replied_at']))$this->addColumn('guestbook_message','replied_at',$this->dateTime());
 }
 public function safeDown(){return false;}
}
