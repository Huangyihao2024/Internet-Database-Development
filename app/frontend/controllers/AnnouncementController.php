<?php
/**
 * 绿茵数据团队：frontend/controllers/AnnouncementController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace frontend\controllers;
use common\models\Announcement;
class AnnouncementController extends \yii\web\Controller {
 public function actionIndex() { $pages=new \yii\data\Pagination(['totalCount'=>Announcement::published()->count(),'pageSize'=>10]);return $this->render('index',['models'=>Announcement::published()->orderBy(['published_at'=>SORT_DESC,'id'=>SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages]); }
 public function actionView(int $id) { $m=Announcement::published()->andWhere(['id'=>$id])->one();if (!$m) throw new \yii\web\NotFoundHttpException('公告不存在或未发布');return $this->render('view',['model'=>$m]); }
}
