<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_squad".
 *
 * @property int $id 主键
 * @property int $tournament_id 届次
 * @property int $team_id 国家队
 * @property int $player_id 球员
 * @property int|null $group_id 所属小组
 * @property int|null $shirt_number 球衣号码
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataGroup $group
 * @property WcDataPlayer $player
 * @property WcDataTeam $team
 * @property WcDataTournament $tournament
 */
class Squad extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_squad';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['group_id', 'shirt_number'], 'default', 'value' => null],
            [['tournament_id', 'team_id', 'player_id'], 'required'],
            [['tournament_id', 'team_id', 'player_id', 'group_id', 'shirt_number'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['tournament_id', 'team_id', 'player_id'], 'unique', 'targetAttribute' => ['tournament_id', 'team_id', 'player_id']],
            [['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTournament::class, 'targetAttribute' => ['tournament_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTeam::class, 'targetAttribute' => ['team_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataPlayer::class, 'targetAttribute' => ['player_id' => 'id']],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataGroup::class, 'targetAttribute' => ['group_id' => 'id']],
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
            'team_id' => 'Team ID',
            'player_id' => 'Player ID',
            'group_id' => 'Group ID',
            'shirt_number' => 'Shirt Number',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[Player]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPlayer()
    {
        return $this->hasOne(WcDataPlayer::class, ['id' => 'player_id']);
    }

    /**
     * Gets query for [[Team]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeam()
    {
        return $this->hasOne(WcDataTeam::class, ['id' => 'team_id']);
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

}
