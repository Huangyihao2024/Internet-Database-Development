<?php
/**
 * 绿茵数据团队：common/models/AssignmentFile.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\models;
class AssignmentFile extends \yii\db\ActiveRecord {
 public static function tableName() { return 'wc_sys_assignment'; }
 public function rules() { return [[['title','relative_path','scope'], 'required'], ['scope','in','range'=>['team','personal']]]; }
 public function attributeLabels() { return ['title'=>'作业名称','scope'=>'类别']; }

 public function resolvedPath(): ?string {
  $rel=$this->relative_path;
  if (!preg_match('~^(team|personal)/[^\\\\/:]+$~u',$rel) || str_contains($rel,'..')) return null;
  $base=realpath(\Yii::getAlias('@common').'/../../data');
  $path=realpath($base.DIRECTORY_SEPARATOR.$rel);
  if (!$path || !is_file($path) || !str_starts_with($path,$base.DIRECTORY_SEPARATOR)) return null;
  if (!hash_equals($this->sha256,hash_file('sha256',$path))) return null;
  return $path;
 }

}
