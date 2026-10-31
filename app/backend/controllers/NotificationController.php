<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace backend\controllers;
use common\models\Notification;
class NotificationController extends \common\controllers\AdminController {
 public function actionIndex(){return $this->render('index',['provider'=>new \yii\data\ActiveDataProvider(['query'=>Notification::find()->with('recipient')->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>15]])]);}
 public function actionView(int $id){$n=Notification::findOne($id);if(!$n)throw new \yii\web\NotFoundHttpException('通知不存在');return $this->render('view',['model'=>$n]);}
}
