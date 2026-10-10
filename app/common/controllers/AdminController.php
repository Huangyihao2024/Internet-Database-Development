<?php
/**
 * 知行学习团队：common/controllers/AdminController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\controllers;
use Yii;
class AdminController extends \yii\web\Controller {
 public function behaviors() { return [
  'access'=>['class'=>\yii\filters\AccessControl::class,'rules'=>[['allow'=>true,'roles'=>['@'],'matchCallback'=>fn()=>Yii::$app->user->identity->isAdmin]]],
  'verbs'=>['class'=>\yii\filters\VerbFilter::class,'actions'=>['delete'=>['POST']]],
 ]; }
}
