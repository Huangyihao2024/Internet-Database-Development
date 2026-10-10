<?php
/**
 * 知行学习团队：backend/controllers/SiteController.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace backend\controllers;
use common\models\{MemberProfile,Announcement,AssignmentFile};
class SiteController extends \common\controllers\AuthController {
 public function actionIndex() { return $this->render('index'); }
}
