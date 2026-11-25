<?php
/**
 * MatchEvent：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class MatchEvent extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_event'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['match_id','team_id','minute','type'], 'required'],
        [['source_key'], 'unique', 'targetAttribute'=>['source_key']],
        [['match_id','player_id','team_id','minute','added_minute','type','source_key'], 'safe'],
        [['match_id','player_id','team_id','minute','added_minute'], 'integer'],
        [[], 'safe'],
        [['type'], 'string', 'max'=>30],
        [['source_key'], 'string', 'max'=>160],
        [['type'], 'in', 'range'=>['goal','penalty_goal','own_goal','yellow_card','red_card','shootout_goal','shootout_miss']],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取match关联。 */
    public function getMatch() { return $this->hasOne(FootballMatch::class, ['id'=>'match_id']); }
    /** 获取player关联。 */
    public function getPlayer() { return $this->hasOne(Player::class, ['id'=>'player_id']); }
    /** 获取team关联。 */
    public function getTeam() { return $this->hasOne(NationalTeam::class, ['id'=>'team_id']); }
}
