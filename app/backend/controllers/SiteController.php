<?php
/**
 * 绿茵数据团队：backend/controllers/SiteController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace backend\controllers;
use common\models\{MemberProfile,Announcement,AssignmentFile};
class SiteController extends \common\controllers\AuthController {
 public function actionIndex() { return $this->render('index',['counts'=>['成员资料'=>MemberProfile::find()->count(),'公告'=>Announcement::find()->count(),'作业文件'=>AssignmentFile::find()->count()]]); }
}
