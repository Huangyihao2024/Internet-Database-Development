<?php
/**
 * 知行学习团队：backend/controllers/AnnouncementController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace backend\controllers;
use Yii;
use common\models\Announcement;
class AnnouncementController extends \common\controllers\AdminController {
 public function actionIndex() { $p=new \yii\data\ActiveDataProvider(['query'=>Announcement::find()->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>10]]);return $this->render('index',['provider'=>$p]); }
 public function actionCreate() { return $this->edit(new Announcement()); }
 public function actionUpdate(int $id) { return $this->edit($this->find($id)); }
 private function edit($m) {
  if ($m->isNewRecord) { $m->author_id=Yii::$app->user->id;$m->status='draft'; }
  if ($m->load(Yii::$app->request->post()) && $m->validate()) {
   $m->published_at=$m->status==='published'?($m->published_at?:date('Y-m-d H:i:s')):null;
   $m->save(false);Yii::$app->session->setFlash('success','公告已保存。');return $this->redirect(['index']);
  }return $this->render('edit',['model'=>$m]);
 }
 private function find($id) { $m=Announcement::findOne($id);if (!$m) throw new \yii\web\NotFoundHttpException('公告不存在');return $m; }
 public function actionDelete(int $id) { $this->find($id)->delete();return $this->redirect(['index']); }
}
