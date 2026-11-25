<?php
/**
 * Squad：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class Squad extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_squad'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['tournament_id','team_id','player_id'], 'required'],
        [['tournament_id','team_id','player_id'], 'unique', 'targetAttribute'=>['tournament_id','team_id','player_id']],
        [['tournament_id','team_id','player_id','group_id','shirt_number'], 'safe'],
        [['tournament_id','team_id','player_id','group_id','shirt_number'], 'integer'],
        [[], 'safe'],
        [['shirt_number'], 'integer', 'min'=>1, 'max'=>99],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取team关联。 */
    public function getTeam() { return $this->hasOne(NationalTeam::class, ['id'=>'team_id']); }
    /** 获取player关联。 */
    public function getPlayer() { return $this->hasOne(Player::class, ['id'=>'player_id']); }
    /** 获取tournament关联。 */
    public function getTournament() { return $this->hasOne(Tournament::class, ['id'=>'tournament_id']); }
}
