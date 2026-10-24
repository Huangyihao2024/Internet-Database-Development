<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace backend\controllers;
use Yii;
use common\models\{FanPost,FanComment};
class ModerationController extends \common\controllers\AdminController {
 private function type($kind){if(!in_array($kind,['post','comment'],true))throw new \yii\web\BadRequestHttpException('审核类型无效');return $kind==='post'?FanPost::class:FanComment::class;}
 public function actionIndex($kind='post'){$class=$this->type($kind);return $this->render('index',['kind'=>$kind,'provider'=>new \yii\data\ActiveDataProvider(['query'=>$class::find()->with('author')->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>15]])]);}
 public function actionUpdate(int $id,$kind='post'){
  $class=$this->type($kind);$m=$class::findOne($id);if(!$m)throw new \yii\web\NotFoundHttpException('内容不存在');
  if(Yii::$app->request->isPost){$status=Yii::$app->request->post('status');if(!in_array($status,['pending','published','rejected','archived'],true))throw new \yii\web\BadRequestHttpException('审核状态无效');$m->status=$status;$m->save(false);
   $ok=\common\services\NotificationService::notifyUser((int)$m->user_id,'绿茵视界审核通知',($kind==='post'?'话题':'评论').' #'.$m->id.' 的处理状态为 '.$status.'。');Yii::$app->session->setFlash($ok?'success':'warning',$ok?'审核已保存，通知已生成。':'审核已保存，通知暂未投递。');return $this->redirect(['index','kind'=>$kind]);
  }return $this->render('update',['model'=>$m,'kind'=>$kind]);
 }
}
