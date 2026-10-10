<?php
/**
 * 绿茵数据团队：common/models/MemberProfile.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\models;
class MemberProfile extends \yii\db\ActiveRecord {
 public static function tableName() { return 'wc_sys_member'; }
 public function getUser(){return $this->hasOne(User::class,['id'=>'user_id']);}
 public function rules() { return [[['display_name'], 'required'], [['display_name'], 'string', 'max'=>100], [['student_number'], 'string', 'max'=>32], [['bio'], 'string', 'max'=>5000]]; }
 public function attributeLabels() { return ['display_name'=>'姓名','student_number'=>'学号','bio'=>'个人介绍']; }

}
