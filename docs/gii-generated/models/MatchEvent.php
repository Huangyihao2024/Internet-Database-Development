<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_event".
 *
 * @property int $id 主键
 * @property int $match_id 比赛编号
 * @property int|null $player_id 球员
 * @property int $team_id 所属队
 * @property int $minute 分钟
 * @property int $added_minute 补时分钟
 * @property string $type 事件类型
 * @property string|null $source_key 来源去重键
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataMatch $match
 * @property WcDataPlayer $player
 * @property WcDataTeam $team
 */
class MatchEvent extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['player_id', 'source_key'], 'default', 'value' => null],
            [['added_minute'], 'default', 'value' => 0],
            [['match_id', 'team_id', 'minute', 'type'], 'required'],
            [['match_id', 'player_id', 'team_id', 'minute', 'added_minute'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['type'], 'string', 'max' => 30],
            [['source_key'], 'string', 'max' => 160],
            [['source_key'], 'unique'],
            [['match_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataMatch::class, 'targetAttribute' => ['match_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataPlayer::class, 'targetAttribute' => ['player_id' => 'id']],
            [['team_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTeam::class, 'targetAttribute' => ['team_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'match_id' => 'Match ID',
            'player_id' => 'Player ID',
            'team_id' => 'Team ID',
            'minute' => 'Minute',
            'added_minute' => 'Added Minute',
            'type' => 'Type',
            'source_key' => 'Source Key',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Match]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMatch()
    {
        return $this->hasOne(WcDataMatch::class, ['id' => 'match_id']);
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

}
