<?php
/**
 * Stadium：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class Stadium extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_stadium'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['name','city'], 'required'],
        [['name','city'], 'unique', 'targetAttribute'=>['name','city']],
        [['name','city','capacity','source_url'], 'safe'],
        [['capacity'], 'integer'],
        [[], 'safe'],
        [['name'], 'string', 'max'=>160],
        [['city'], 'string', 'max'=>100],
        [['source_url'], 'string', 'max'=>1000],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
}
