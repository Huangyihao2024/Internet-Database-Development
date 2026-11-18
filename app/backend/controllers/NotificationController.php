<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace backend\controllers;
use Yii;
class NotificationController extends \common\controllers\AdminController {
 public function actionIndex(){
  $files=glob(Yii::getAlias('@runtime/mail').'/*.eml')?:[];rsort($files);
  return $this->render('index',['files'=>$files]);
 }
}
