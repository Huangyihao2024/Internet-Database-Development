<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace frontend\controllers;
use Yii;
use common\models\{FanPost,FanComment,AuditLog};
class CommunityController extends \yii\web\Controller {
 public function behaviors(){return ['verbs'=>['class'=>\yii\filters\VerbFilter::class,'actions'=>['comment'=>['POST'],'withdraw'=>['POST']]]];}
 private function auth(){if(Yii::$app->user->isGuest)throw new \yii\web\ForbiddenHttpException('请先登录。');}
 private function post(int $id,bool $public=true){$m=FanPost::findOne($id);if(!$m || ($public && $m->status!=='published'))throw new \yii\web\NotFoundHttpException('话题不存在');return $m;}
 public function actionIndex(){
  $query=FanPost::find()->with('author')->where(['status'=>'published']);$q=Yii::$app->request->get('q','');if(!is_string($q))throw new \yii\web\BadRequestHttpException('检索条件无效');$q=mb_substr(trim($q),0,100);if($q!=='')$query->andWhere(['like','title',$q]);
  $pages=new \yii\data\Pagination(['totalCount'=>$query->count(),'pageSize'=>10]);return $this->render('index',['posts'=>$query->orderBy(['id'=>SORT_DESC])->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages,'q'=>$q]);
 }
 public function actionView(int $id){$p=$this->post($id);$query=FanComment::find()->with('author')->where(['post_id'=>$id,'status'=>'published'])->andWhere(['or',['parent_id'=>null],['parent_id'=>FanComment::find()->select('id')->where(['post_id'=>$id,'status'=>'published'])]]);$pages=new \yii\data\Pagination(['totalCount'=>$query->count(),'pageSize'=>15]);return $this->render('view',['post'=>$p,'comments'=>$query->orderBy('id')->offset($pages->offset)->limit($pages->limit)->all(),'pages'=>$pages,'model'=>new FanComment()]);}
 public function actionCreate(){ $this->auth();$m=new FanPost();return $this->edit($m); }
 public function actionEdit(int $id){$this->auth();$m=$this->post($id,false);if($m->user_id!=Yii::$app->user->id)throw new \yii\web\ForbiddenHttpException('只能编辑自己的话题');if($m->status==='archived')throw new \yii\web\NotFoundHttpException('话题已撤下');return $this->edit($m);}
 private function edit($m){
  if($m->load(Yii::$app->request->post())&&$m->validate()){
   Yii::$app->db->transaction(function() use($m){$m->user_id=Yii::$app->user->id;$m->status='pending';$m->save(false);AuditLog::record('submit','fan_post',$m->id);});
   Yii::$app->session->setFlash('success','话题已提交，审核通过后公开。');return $this->redirect(['mine']);
  }return $this->render('edit',['model'=>$m]);
 }
 public function actionMine(){ $this->auth();$provider=new \yii\data\ActiveDataProvider(['query'=>FanPost::find()->where(['user_id'=>Yii::$app->user->id])->orderBy(['id'=>SORT_DESC]),'pagination'=>['pageSize'=>10]]);return $this->render('mine',['provider'=>$provider]); }
 public function actionWithdraw(int $id){$this->auth();$p=$this->post($id,false);if($p->user_id!=Yii::$app->user->id)throw new \yii\web\ForbiddenHttpException('不能操作他人话题');$p->status='archived';$p->save(false);AuditLog::record('withdraw','fan_post',$id);return $this->redirect(['mine']);}
 public function actionComment(int $id){
  $this->auth();$p=$this->post($id);$m=new FanComment();$m->post_id=$id;$m->user_id=Yii::$app->user->id;$m->status='pending';
  if(!$m->load(Yii::$app->request->post())||!$m->validate()){Yii::$app->session->setFlash('error',implode('；',$m->getFirstErrors())?:'评论内容无效');return $this->redirect(['view','id'=>$id]);}
  Yii::$app->db->transaction(function() use($m){$m->save(false);AuditLog::record('submit','fan_comment',$m->id);});Yii::$app->session->setFlash('success','评论已提交，审核通过后展示。');return $this->redirect(['view','id'=>$id]);
 }
}
