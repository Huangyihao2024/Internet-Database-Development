<?php
/**
 * Player：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class Player extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_player'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['name','nationality_team_id'], 'required'],
        [['name','nationality_team_id'], 'unique', 'targetAttribute'=>['name','nationality_team_id']],
        [['name','nationality_team_id','birth_date','position','source_url'], 'safe'],
        [['nationality_team_id'], 'integer'],
        [['birth_date'], 'safe'],
        [['name'], 'string', 'max'=>160],
        [['position'], 'string', 'max'=>40],
        [['source_url'], 'string', 'max'=>1000],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取team关联。 */
    public function getTeam() { return $this->hasOne(NationalTeam::class, ['id'=>'nationality_team_id']); }
    /** 获取squads关联。 */
    public function getSquads() { return $this->hasMany(Squad::class, ['player_id'=>'id']); }
    /** 获取events关联。 */
    public function getEvents() { return $this->hasMany(MatchEvent::class, ['player_id'=>'id']); }
}
