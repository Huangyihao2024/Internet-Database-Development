<?php
/**
 * 知行学习团队：backend/controllers/MemberController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace backend\controllers;
use Yii;
use common\models\MemberProfile;
class MemberController extends \common\controllers\AdminController {
 public function actionIndex() { return $this->render('index',['models'=>MemberProfile::find()->orderBy('id')->all()]); }
 public function actionUpdate(int $id) { $m=MemberProfile::findOne($id);if (!$m) throw new \yii\web\NotFoundHttpException('成员不存在');if ($m->load(Yii::$app->request->post()) && $m->save()) { Yii::$app->session->setFlash('success','成员资料已更新。');return $this->redirect(['index']); }return $this->render('update',['model'=>$m]); }
}
