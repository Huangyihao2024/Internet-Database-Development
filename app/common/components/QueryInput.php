<?php
/** 查询参数约束；公共接口维护，AI辅助编写。 */
namespace common\components;
class QueryInput {
 public static function check(array $text,array $ids): void {
  $r=\Yii::$app->request;
  foreach(array_merge($text,$ids) as $key){$v=$r->get($key);if($v===null||$v==='')continue;
   if(!is_string($v)|| (in_array($key,$ids,true)&&filter_var($v,FILTER_VALIDATE_INT,['options'=>['min_range'=>1]])===false))throw new \yii\web\BadRequestHttpException('查询参数无效：'.$key);
  }
 }
}
