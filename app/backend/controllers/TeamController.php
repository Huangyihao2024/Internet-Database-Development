<?php
/**
 * 知行学习团队：backend/controllers/TeamController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace backend\controllers;
use Yii;
use common\models\Team;
class TeamController extends \common\controllers\AdminController {
 public function actionUpdate() { $m=Team::find()->one();if (!$m) throw new \yii\web\NotFoundHttpException('团队未初始化');if ($m->load(Yii::$app->request->post()) && $m->save()) { Yii::$app->session->setFlash('success','团队介绍已更新。');return $this->refresh(); }return $this->render('update',['model'=>$m]); }
}
