<?php
/**
 * 绿茵数据团队：frontend/controllers/DownloadController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace frontend\controllers;
use common\models\AssignmentFile;
use Yii;
class DownloadController extends \yii\web\Controller {
 public function actionIndex() { return $this->render('index',['files'=>AssignmentFile::find()->orderBy(['scope'=>SORT_DESC,'id'=>SORT_ASC])->all()]); }
 public function actionFile(int $id) { $f=AssignmentFile::findOne($id);if (!$f || !($path=$f->resolvedPath())) throw new \yii\web\NotFoundHttpException('文件不可用');return Yii::$app->response->sendFile($path,basename($path),['inline'=>false]); }
}
