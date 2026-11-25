<?php
/**
 * Stage：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class Stage extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_stage'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['tournament_id','name','sort_order'], 'required'],
        [['tournament_id','name'], 'unique', 'targetAttribute'=>['tournament_id','name']],
        [['tournament_id','name','sort_order'], 'safe'],
        [['tournament_id','sort_order'], 'integer'],
        [[], 'safe'],
        [['name'], 'string', 'max'=>80],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取tournament关联。 */
    public function getTournament() { return $this->hasOne(Tournament::class, ['id'=>'tournament_id']); }
    /** 获取matches关联。 */
    public function getMatches() { return $this->hasMany(FootballMatch::class, ['stage_id'=>'id']); }
}
