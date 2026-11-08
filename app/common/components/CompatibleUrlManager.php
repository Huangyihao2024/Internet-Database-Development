<?php
/** URL兼容与规则：本人负责的公共模块，AI辅助。 */
namespace common\components;
class CompatibleUrlManager extends \yii\web\UrlManager {
 public $enablePrettyUrl=true;
 public $showScriptName=true;
 public function parseRequest($request) {
  if (is_string($r=$request->get('r')) && $r!=='') return [$r,$request->getQueryParams()];
  return parent::parseRequest($request);
 }
}
