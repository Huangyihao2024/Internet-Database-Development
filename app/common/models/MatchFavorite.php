<?php
/**
 * MatchFavorite：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class MatchFavorite extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_fan_match_favorite'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['user_id','match_id'], 'required'],
        [['user_id','match_id'], 'unique', 'targetAttribute'=>['user_id','match_id']],
        [['user_id','match_id'], 'safe'],
        [['user_id','match_id'], 'integer'],
        [[], 'safe'],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取match关联。 */
    public function getMatch() { return $this->hasOne(FootballMatch::class, ['id'=>'match_id']); }
}
