<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_match".
 *
 * @property int $id 主键
 * @property int $tournament_id 届次
 * @property int $stage_id 阶段
 * @property int|null $group_id 小组
 * @property int $stadium_id 球场
 * @property int $match_number 届内比赛编号
 * @property int $home_team_id 主队
 * @property int $away_team_id 客队
 * @property string $kicks_off_at UTC开赛时间
 * @property int|null $normal_home 常规主队进球
 * @property int|null $normal_away 常规客队进球
 * @property int|null $extra_home 加时主队进球
 * @property int|null $extra_away 加时客队进球
 * @property int|null $penalty_home 点球大战主队命中
 * @property int|null $penalty_away 点球大战客队命中
 * @property string $status 比赛状态
 * @property string|null $source_key 来源去重键
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataTeam $awayTeam
 * @property WcDataGroup $group
 * @property WcDataTeam $homeTeam
 * @property WcDataStadium $stadium
 * @property WcDataStage $stage
 * @property WcDataTournament $tournament
 * @property WcSysUser[] $users
 * @property WcDataEvent[] $wcDataEvents
 * @property WcFanMatchFavorite[] $wcFanMatchFavorites
 * @property WcFanPost[] $wcFanPosts
 */
class FootballMatch extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_match';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'normal_home', 'normal_away', 'extra_home', 'extra_away', 'penalty_home', 'penalty_away', 'source_key'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'scheduled'],
            [['tournament_id', 'stage_id', 'stadium_id', 'match_number', 'home_team_id', 'away_team_id', 'kicks_off_at'], 'required'],
            [['tournament_id', 'stage_id', 'group_id', 'stadium_id', 'match_number', 'home_team_id', 'away_team_id', 'normal_home', 'normal_away', 'extra_home', 'extra_away', 'penalty_home', 'penalty_away'], 'integer'],
            [['kicks_off_at', 'created_at', 'updated_at'], 'safe'],
            [['status'], 'string', 'max' => 20],
            [['source_key'], 'string', 'max' => 160],
            [['tournament_id', 'match_number'], 'unique', 'targetAttribute' => ['tournament_id', 'match_number']],
            [['source_key'], 'unique'],
            [['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTournament::class, 'targetAttribute' => ['tournament_id' => 'id']],
            [['stage_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataStage::class, 'targetAttribute' => ['stage_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataGroup::class, 'targetAttribute' => ['group_id' => 'id']],
            [['stadium_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataStadium::class, 'targetAttribute' => ['stadium_id' => 'id']],
            [['home_team_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTeam::class, 'targetAttribute' => ['home_team_id' => 'id']],
            [['away_team_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTeam::class, 'targetAttribute' => ['away_team_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tournament_id' => 'Tournament ID',
            'stage_id' => 'Stage ID',
            'group_id' => 'Group ID',
            'stadium_id' => 'Stadium ID',
            'match_number' => 'Match Number',
            'home_team_id' => 'Home Team ID',
            'away_team_id' => 'Away Team ID',
            'kicks_off_at' => 'Kicks Off At',
            'normal_home' => 'Normal Home',
            'normal_away' => 'Normal Away',
            'extra_home' => 'Extra Home',
            'extra_away' => 'Extra Away',
            'penalty_home' => 'Penalty Home',
            'penalty_away' => 'Penalty Away',
            'status' => 'Status',
            'source_key' => 'Source Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AwayTeam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAwayTeam()
    {
        return $this->hasOne(WcDataTeam::class, ['id' => 'away_team_id']);
    }

    /**
     * Gets query for [[Group]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(WcDataGroup::class, ['id' => 'group_id']);
    }

    /**
     * Gets query for [[HomeTeam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHomeTeam()
    {
        return $this->hasOne(WcDataTeam::class, ['id' => 'home_team_id']);
    }

    /**
     * Gets query for [[Stadium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStadium()
    {
        return $this->hasOne(WcDataStadium::class, ['id' => 'stadium_id']);
    }

    /**
     * Gets query for [[Stage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStage()
    {
        return $this->hasOne(WcDataStage::class, ['id' => 'stage_id']);
    }

    /**
     * Gets query for [[Tournament]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(WcDataTournament::class, ['id' => 'tournament_id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(WcSysUser::class, ['id' => 'user_id'])->viaTable('wc_fan_match_favorite', ['match_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataEvents()
    {
        return $this->hasMany(WcDataEvent::class, ['match_id' => 'id']);
    }

    /**
     * Gets query for [[WcFanMatchFavorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcFanMatchFavorites()
    {
        return $this->hasMany(WcFanMatchFavorite::class, ['match_id' => 'id']);
    }

    /**
     * Gets query for [[WcFanPosts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcFanPosts()
    {
        return $this->hasMany(WcFanPost::class, ['match_id' => 'id']);
    }

}
