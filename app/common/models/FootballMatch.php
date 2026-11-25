<?php
/**
 * FootballMatch：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\models;
class FootballMatch extends \yii\db\ActiveRecord {
    /** 返回现有业务表名。 */
    public static function tableName() { return 'wc_data_match'; }
    /** 验证必填、唯一及字段类型。 */
    public function rules() { return [
        [['tournament_id','stage_id','stadium_id','match_number','home_team_id','away_team_id','kicks_off_at'], 'required'],
        [['tournament_id','match_number'], 'unique', 'targetAttribute'=>['tournament_id','match_number']],
        [['tournament_id','stage_id','group_id','stadium_id','match_number','home_team_id','away_team_id','kicks_off_at','normal_home','normal_away','extra_home','extra_away','penalty_home','penalty_away','status','source_key'], 'safe'],
        [['tournament_id','stage_id','group_id','stadium_id','match_number','home_team_id','away_team_id','normal_home','normal_away','extra_home','extra_away','penalty_home','penalty_away'], 'integer'],
        [['kicks_off_at'], 'safe'],
        [['status'], 'string', 'max'=>20],
        [['source_key'], 'string', 'max'=>160],
        [['status'], 'in', 'range'=>['scheduled','finished','postponed']],
        [['away_team_id'], 'compare', 'compareAttribute'=>'home_team_id', 'operator'=>'!='],
        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }
    /** 获取tournament关联。 */
    public function getTournament() { return $this->hasOne(Tournament::class, ['id'=>'tournament_id']); }
    /** 获取stage关联。 */
    public function getStage() { return $this->hasOne(Stage::class, ['id'=>'stage_id']); }
    /** 获取group关联。 */
    public function getGroup() { return $this->hasOne(TournamentGroup::class, ['id'=>'group_id']); }
    /** 获取stadium关联。 */
    public function getStadium() { return $this->hasOne(Stadium::class, ['id'=>'stadium_id']); }
    /** 获取homeTeam关联。 */
    public function getHomeTeam() { return $this->hasOne(NationalTeam::class, ['id'=>'home_team_id']); }
    /** 获取awayTeam关联。 */
    public function getAwayTeam() { return $this->hasOne(NationalTeam::class, ['id'=>'away_team_id']); }
    /** 获取events关联。 */
    public function getEvents() { return $this->hasMany(MatchEvent::class, ['match_id'=>'id'])->orderBy(['minute'=>SORT_ASC,'added_minute'=>SORT_ASC,'id'=>SORT_ASC]); }
}
