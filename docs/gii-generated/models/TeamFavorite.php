<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_fan_team_favorite".
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $team_id 球队
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataTeam $team
 * @property WcSysUser $user
 */
class TeamFavorite extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_fan_team_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'team_id'], 'required'],
            [['user_id', 'team_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'team_id'], 'unique', 'targetAttribute' => ['user_id', 'team_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcSysUser::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'user_id' => 'User ID',
            'team_id' => 'Team ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WcSysUser::class, ['id' => 'user_id']);
    }

}
