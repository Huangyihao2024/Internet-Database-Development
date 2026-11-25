<?php
/**
 * NationalTeam：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class NationalTeam extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_team'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['name','fifa_code'], 'required'],
        [['fifa_code'], 'unique', 'targetAttribute'=>['fifa_code']],
        [['name','fifa_code','confederation','description'], 'safe'],
        [[], 'integer'],
        [[], 'safe'],
        [['name'], 'string', 'max'=>120],
        [['fifa_code'], 'string', 'max'=>3],
        [['confederation'], 'string', 'max'=>30],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取players关联。 */
    public function getPlayers() { return $this->hasMany(Player::class, ['nationality_team_id'=>'id']); }
    /** 获取squads关联。 */
    public function getSquads() { return $this->hasMany(Squad::class, ['team_id'=>'id']); }
}
