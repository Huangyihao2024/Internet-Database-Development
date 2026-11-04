<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_team".
 *
 * @property int $id 主键
 * @property string $name 队名
 * @property string $fifa_code FIFA代码
 * @property string|null $confederation 洲足联
 * @property string|null $description 介绍
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcSysUser[] $users
 * @property WcDataEvent[] $wcDataEvents
 * @property WcDataMatch[] $wcDataMatches
 * @property WcDataMatch[] $wcDataMatches0
 * @property WcDataPlayer[] $wcDataPlayers
 * @property WcDataSquad[] $wcDataSquads
 * @property WcFanTeamFavorite[] $wcFanTeamFavorites
 */
class NationalTeam extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_team';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['confederation', 'description'], 'default', 'value' => null],
            [['name', 'fifa_code'], 'required'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 120],
            [['fifa_code'], 'string', 'max' => 3],
            [['confederation'], 'string', 'max' => 30],
            [['fifa_code'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'fifa_code' => 'Fifa Code',
            'confederation' => 'Confederation',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(WcSysUser::class, ['id' => 'user_id'])->viaTable('wc_fan_team_favorite', ['team_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataEvents()
    {
        return $this->hasMany(WcDataEvent::class, ['team_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataMatches()
    {
        return $this->hasMany(WcDataMatch::class, ['home_team_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataMatches0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataMatches0()
    {
        return $this->hasMany(WcDataMatch::class, ['away_team_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataPlayers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataPlayers()
    {
        return $this->hasMany(WcDataPlayer::class, ['nationality_team_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataSquads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataSquads()
    {
        return $this->hasMany(WcDataSquad::class, ['team_id' => 'id']);
    }

    /**
     * Gets query for [[WcFanTeamFavorites]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcFanTeamFavorites()
    {
        return $this->hasMany(WcFanTeamFavorite::class, ['team_id' => 'id']);
    }

}
