<?php
/**
 * 绿茵数据团队：common/models/DeveloperTeam.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\models;
class DeveloperTeam extends \yii\db\ActiveRecord {
 public static function tableName() { return 'wc_sys_team'; }
 public function rules() { return [[['name'], 'required'], [['name'], 'string', 'max'=>100], [['description'], 'string', 'max'=>10000]]; }
 public function attributeLabels() { return ['name'=>'团队名称','description'=>'团队介绍']; }

}
