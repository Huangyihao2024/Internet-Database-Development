<?php
/**
 * Tournament：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class Tournament extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_tournament'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['year','name','starts_at','ends_at'], 'required'],
        [['year'], 'unique', 'targetAttribute'=>['year']],
        [['year','name','starts_at','ends_at','source_url'], 'safe'],
        [['year'], 'integer'],
        [['starts_at','ends_at'], 'safe'],
        [['name'], 'string', 'max'=>160],
        [['source_url'], 'string', 'max'=>1000],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取stages关联。 */
    public function getStages() { return $this->hasMany(Stage::class, ['tournament_id'=>'id']); }
    /** 获取groups关联。 */
    public function getGroups() { return $this->hasMany(TournamentGroup::class, ['tournament_id'=>'id']); }
    /** 获取matches关联。 */
    public function getMatches() { return $this->hasMany(FootballMatch::class, ['tournament_id'=>'id']); }
}
