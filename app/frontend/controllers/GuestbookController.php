<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */
namespace frontend\controllers;
use Yii;
use common\models\{GuestbookMessage,AuditLog};
class GuestbookController extends \yii\web\Controller {
 public function actionIndex(){
  $m=new GuestbookMessage();
  if(Yii::$app->request->isPost){
   if(Yii::$app->user->isGuest)return Yii::$app->user->loginRequired();
   if($m->load(Yii::$app->request->post()) && $m->validate()){
    Yii::$app->db->transaction(function() use($m){$m->user_id=Yii::$app->user->id;$m->status='pending';$m->save(false);AuditLog::record('submit','guestbook_message',$m->id);});
    Yii::$app->session->setFlash('success','留言已提交，审核通过后公开。');return $this->refresh();
   }
  }
  $query=GuestbookMessage::find()->with('author')->where(['status'=>'published']);$pages=new \yii\data\Pagination(['totalCount'=>$query->count(),'pageSize'=>10]);
  return $this->render('index',['model'=>$m,'messages'=>$query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages]);
 }
}
