<?php
/**
 * 知行学习团队开发数据与下载登记初始化。
 * 负责人：黄奕浩 2413581；AI辅助实现。
 */
namespace console\controllers;
use Yii;
use common\models\{User,Team,MemberProfile,Announcement,AssignmentFile};
class TeamSetupController extends \yii\console\Controller {
 public function actionUpgrade(){
  require_once \Yii::getAlias('@console').'/migrations/m261118_020000_guestbook_reply.php';
  (new \console\migrations\m261118_020000_guestbook_reply(['db'=>Yii::$app->db]))->safeUp();
  MemberProfile::deleteAll(['student_number'=>'2413628']);
  AssignmentFile::deleteAll(['like','relative_path','2413581_2411302_2413628']);
  User::updateAll(['status'=>0],['username'=>'huangleren']);
  if(\common\models\GuestbookMessage::find()->count()==0){
   $uid=User::findByUsername('huangyihao')->id;
   for($i=1;$i<=5;$i++){$m=new \common\models\GuestbookMessage();$m->user_id=$uid;$m->content='【演示留言】学习交流示例'.$i.'，用于展示审核流程。';$m->status=$i<=2?'published':'pending';$m->save(false);}
  }
  echo "Two-member team and guestbook upgrade completed.\n";
 }
 public function actionSeed() {
  $path=getenv('ZHIXING_ACCOUNT_FILE');
  if (!$path || !is_file($path)) throw new \RuntimeException('请通过本地初始化脚本提供账号文件。');
  $accounts=json_decode(file_get_contents($path),true,512,JSON_THROW_ON_ERROR);
  Yii::$app->db->transaction(function() use($accounts){
   $team=Team::find()->one();
   if (!$team) { $team=new Team();$team->name='知行学习团队';$team->description='以校园学习为起点，把知识整理成资源，把协作沉淀为成果。我们共同建设团队门户，记录每一位成员的学习与实践。';$team->save(false); }
   foreach($accounts as $a) {
    $u=User::findByUsername($a['username']);
    if (!$u) { $u=new User();$u->username=$a['username'];$u->email=$a['username'].'@example.invalid';$u->role=$a['role'];$u->status=10;$u->setPassword($a['password']);$u->generateAuthKey();$u->save(false); }
    if (!MemberProfile::findOne(['user_id'=>$u->id])) { $m=new MemberProfile();$m->user_id=$u->id;$m->team_id=$team->id;$m->display_name=$a['name'];$m->student_number=$a['student_id'];$m->bio=$a['bio'];$m->save(false); }
   }
   if (Announcement::find()->count()==0) {
    foreach(['欢迎来到知行学习团队','学习资源整理约定','团队协作与代码规范','课程作业提交提醒','本周学习交流安排'] as $i=>$title) {
     $m=new Announcement();$m->author_id=User::findByUsername('huangyihao')->id;$m->title=$title;$m->content='【演示公告】'.$title.'。本条为课程设计自拟示例，用于展示公告发布与阅读流程；实际活动和安排以团队确认的信息为准。';$m->status='published';$m->published_at=date('Y-m-d H:i:s',time()-$i*3600);$m->save(false);
    }
   }
  });
  $this->actionUpgrade();$this->actionSyncDownloads();echo "Team sample data initialized.\n";
 }
 public function actionSyncDownloads() {
  $base=Yii::getAlias('@common').'/../../data';
  foreach(AssignmentFile::find()->all() as $old) if (str_contains($old->relative_path,'_公共模块说明(') || !in_array(strtolower(pathinfo($old->relative_path,PATHINFO_EXTENSION)),['pdf','zip','pptx','mp4','avi','rmvb'],true)) $old->delete();
  $uid=User::findByUsername('huangyihao')->id;
  foreach(['team','personal'] as $scope) foreach(glob($base.'/'.$scope.'/*') as $p) {
   if (!is_file($p) || str_starts_with(basename($p),'.') || !in_array(strtolower(pathinfo($p,PATHINFO_EXTENSION)),['pdf','zip','pptx','mp4','avi','rmvb'],true)) continue;
   $rel=$scope.'/'.basename($p);$m=AssignmentFile::findOne(['relative_path'=>$rel])??new AssignmentFile();
   $m->owner_id=$uid;$m->title=basename($p);$m->relative_path=$rel;$m->scope=$scope;$m->sha256=hash_file('sha256',$p);$m->save(false);
  }
  echo "Download registry synchronized.\n";
 }
}
