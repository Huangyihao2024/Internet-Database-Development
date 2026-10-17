<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace frontend\controllers;
use common\models\{DeveloperTeam,MemberProfile};
class DeveloperController extends \yii\web\Controller {
 public function actionIndex(){return $this->render('index',['team'=>DeveloperTeam::find()->one(),'members'=>MemberProfile::find()->joinWith('user')->where(['wc_sys_user.username'=>['huangyihao','wanglezhi'],'wc_sys_user.status'=>10])->all()]);}
 public function actionView(int $id){$m=MemberProfile::findOne($id);if(!$m)throw new \yii\web\NotFoundHttpException('成员不存在');return $this->render('@frontend/views/member/view',['model'=>$m]);}
}
