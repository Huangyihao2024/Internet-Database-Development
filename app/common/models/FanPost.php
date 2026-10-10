<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace common\models;
class FanPost extends \yii\db\ActiveRecord {
 public static function tableName(){return 'wc_fan_post';}
 public function rules(){return [[['title','content'],'required'],[['title','content'],'trim'],['title','string','max'=>200],['content','string','min'=>2,'max'=>15000],['match_id','default','value'=>null],['match_id','integer'],['match_id','exist','targetClass'=>MatchReference::class,'targetAttribute'=>'id']];}
 public function attributeLabels(){return ['title'=>'话题标题','content'=>'话题内容','match_id'=>'关联比赛编号'];}
 public function getAuthor(){return $this->hasOne(User::class,['id'=>'user_id']);}
 public function getComments(){return $this->hasMany(FanComment::class,['post_id'=>'id']);}
}
