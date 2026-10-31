<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace backend\controllers;
class AuditController extends \common\controllers\AdminController {
 public function actionIndex(){return $this->render('index',['provider'=>new \yii\data\ActiveDataProvider(['query'=>\common\models\AuditLog::find()->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>20]])]);}
}
