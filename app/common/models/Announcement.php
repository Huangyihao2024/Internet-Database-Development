<?php
/**
 * 绿茵数据团队：common/models/Announcement.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
namespace common\models;
class Announcement extends \yii\db\ActiveRecord {
 public static function tableName() { return 'wc_content_announcement'; }
 public function rules() { return [[['title','content','status'], 'required'], [['title'], 'string', 'max'=>200], [['content'], 'string', 'max'=>20000], ['status','in','range'=>['draft','published','archived']]]; }
 public function attributeLabels() { return ['title'=>'标题','content'=>'公告内容','status'=>'发布状态']; }
 public static function published() { return self::find()->where(['status'=>'published'])->andWhere(['<=','published_at',date('Y-m-d H:i:s')]); }
}
