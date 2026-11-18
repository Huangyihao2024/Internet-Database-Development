<?php
/** 世界杯公共模块样例和文件登记；本人负责，AI辅助编写。 */
namespace console\controllers;
use Yii;
use common\models\{User,DeveloperTeam,MemberProfile,Announcement,AssignmentFile,FanPost,FanComment};
class WorldcupSetupController extends \yii\console\Controller {
 public function actionEnsureTournament(){
  $id=Yii::$app->db->createCommand('SELECT id FROM wc_data_tournament WHERE year=2022')->queryScalar();
  if(!$id){if(Yii::$app->runAction('worldcup-import/run')!==0)throw new \RuntimeException('世界杯数据初始化失败');}
  $id=Yii::$app->db->createCommand('SELECT id FROM wc_data_tournament WHERE year=2022')->queryScalar();
  $matches=(int)Yii::$app->db->createCommand('SELECT COUNT(*) FROM wc_data_match WHERE tournament_id=:id',[':id'=>$id])->queryScalar();
  if($matches!==64)throw new \RuntimeException('2022赛事应有64场，请在后台检查数据导入；已有记录予以保留。');
  echo "2022 tournament ready: 64 matches. Existing tournament records preserved.\n";
 }
 public function actionSeed(){
  $file=getenv('WORLD_CUP_ACCOUNT_FILE');if(!$file||!is_file($file))throw new \RuntimeException('初始化账号文件不存在');
  $accounts=json_decode(file_get_contents($file),true,512,JSON_THROW_ON_ERROR);
  Yii::$app->db->transaction(function()use($accounts){
   $t=DeveloperTeam::find()->one();if(!$t){$t=new DeveloperTeam();$t->name='绿茵数据团队';$t->description='我们以世界杯为共同话题，用数据梳理比赛，用交流连接球迷。两名开发者共同建设绿茵视界，分别负责公共社区与赛事数据模块。';$t->save(false);}
   foreach($accounts as $a){$u=User::findByUsername($a['username']);if(!$u){$u=new User();$u->username=$a['username'];$u->email=$a['username'].'@example.invalid';$u->role=$a['role'];$u->status=10;$u->setPassword($a['password']);$u->generateAuthKey();$u->save(false);}if(!MemberProfile::findOne(['user_id'=>$u->id])){$m=new MemberProfile();$m->user_id=$u->id;$m->team_id=$t->id;$m->display_name=$a['name'];$m->student_number=$a['student_id'];$m->bio=$a['bio'];$m->save(false);}}
   $admin=User::findByUsername('huangyihao')->id;$member=User::findByUsername('wanglezhi')->id;
   if(Announcement::find()->count()==0)foreach(['欢迎来到绿茵视界','球迷社区交流约定','世界杯话题发布指南','课程成果下载说明','账号与内容管理提示'] as $i=>$title){$m=new Announcement();$m->author_id=$admin;$m->title=$title;$m->content=['欢迎围绕世界杯的比赛、球队和球员分享观点。在这里重温精彩瞬间，与球迷交流。','讨论围绕足球展开，尊重不同的支持立场，避免人身攻击。','发帖请使用明确标题，描述自己的观察；提交后由管理员审核。','开发团队的文档与个人作业统一放在作业下载页面。','请保管好账号；话题、评论和回复均经过审核后公开。'][$i];$m->status='published';$m->published_at=date('Y-m-d H:i:s',time()-$i*3600);$m->save(false);}
   if(FanPost::find()->count()==0){$samples=[['你最难忘的世界杯瞬间是什么？','从一脚射门到终场哨声，世界杯总会留下让人记住的画面。欢迎聊聊你的观赛记忆。'],['支持一支球队，你最看重什么？','是战术风格、球员故事，还是长久以来的情感连接？欢迎交流不同的选择。'],['怎样理解比赛中的控球与反击？','控球和快速转换各有特点。你会从哪些细节观察两支球队的比赛方式？'],['一起整理世界杯观赛笔记','比分之外，跑位、传球与换人同样值得记录。分享一个你喜欢的观赛方法。'],['第一次看世界杯，有哪些入门建议？','对于刚开始了解世界杯的朋友，怎样认识赛制、球队与球员会更轻松？']];foreach($samples as $i=>$v){$p=new FanPost();$p->user_id=$member;$p->title=$v[0];$p->content=$v[1];$p->status='published';$p->save(false);$c=new FanComment();$c->post_id=$p->id;$c->user_id=$admin;$c->content='欢迎大家分享看法，一起聊球。';$c->status='published';$c->save(false);}}
  });$this->actionSyncDownloads();echo "World Cup public and community sample data ready.\n";
 }
 public function actionUpgrade(){echo "World Cup schema ready.\n";}
 public function actionExportSchema(){
  $path=Yii::getAlias('@common').'/../../data/install.sql';preg_match_all('/CREATE TABLE `([^`]+)`/',file_get_contents($path),$m);
  $out="-- World Cup database schema: 20 business tables.\nSET NAMES utf8mb4;\n\n";
  foreach($m[1] as $table){$ddl=Yii::$app->db->createCommand('SHOW CREATE TABLE `'.$table.'`')->queryOne();$values=array_values($ddl);$out.=preg_replace('/ AUTO_INCREMENT=\d+/','',$values[1]).";\n\n";}
  file_put_contents($path,rtrim($out)."\n");echo "World Cup schema exported.\n";
 }
 public function actionSyncDownloads(){
  $root=Yii::getAlias('@common').'/../../data';$u=User::findByUsername('huangyihao');if(!$u)return;
  foreach(['team','personal'] as $scope)foreach(glob($root.'/'.$scope.'/*') as $path){if(!is_file($path)||!in_array(strtolower(pathinfo($path,PATHINFO_EXTENSION)),['pdf','zip','pptx','mp4'],true))continue;$rel=$scope.'/'.basename($path);$m=AssignmentFile::findOne(['relative_path'=>$rel])??new AssignmentFile();$m->owner_id=$u->id;$m->scope=$scope;$m->title=basename($path);$m->relative_path=$rel;$m->sha256=hash_file('sha256',$path);$m->save(false);}
  echo "Coursework registry synchronized.\n";
 }
}
