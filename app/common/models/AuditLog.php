<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace common\models;
use Yii;
class AuditLog extends \yii\db\ActiveRecord {
 public static function tableName(){return 'audit_log';}
 public static function record($action,$type,$id=null){
  if (!(Yii::$app instanceof \yii\web\Application) || Yii::$app->user->isGuest)return;
  $m=new self();$m->actor_id=Yii::$app->user->id;$m->action=substr($action,0,80);$m->object_type=substr($type,0,80);$m->object_id=is_numeric($id)?(int)$id:null;$m->details='不记录口令、令牌或请求正文';
  if(!$m->save(false))throw new \RuntimeException('审计记录保存失败');
 }
}
