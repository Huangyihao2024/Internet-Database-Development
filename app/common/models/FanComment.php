<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */
namespace common\models;
class FanComment extends \yii\db\ActiveRecord {
 public static function tableName(){return 'wc_fan_comment';}
 public function rules(){return [[['content'],'required'],['content','trim'],['content','string','min'=>2,'max'=>4000],['parent_id','default','value'=>null],['parent_id','integer'],['parent_id','checkParent']];}
 public function checkParent($attribute){if($this->parent_id && !self::find()->where(['id'=>$this->parent_id,'post_id'=>$this->post_id,'status'=>'published'])->exists())$this->addError($attribute,'回复对象不存在或不属于本话题。');}
 public function attributeLabels(){return ['content'=>'评论内容','parent_id'=>'回复对象'];}
 public function getAuthor(){return $this->hasOne(User::class,['id'=>'user_id']);}
}
