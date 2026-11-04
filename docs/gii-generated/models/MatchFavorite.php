<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_fan_match_favorite".
 *
 * @property int $id 主键
 * @property int $user_id 用户
 * @property int $match_id 比赛
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataMatch $match
 * @property WcSysUser $user
 */
class MatchFavorite extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_fan_match_favorite';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'match_id'], 'required'],
            [['user_id', 'match_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_id', 'match_id'], 'unique', 'targetAttribute' => ['user_id', 'match_id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcSysUser::class, 'targetAttribute' => ['user_id' => 'id']],
            [['match_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataMatch::class, 'targetAttribute' => ['match_id' => 'id']],
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
            'match_id' => 'Match ID',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(WcSysUser::class, ['id' => 'user_id']);
    }

}
