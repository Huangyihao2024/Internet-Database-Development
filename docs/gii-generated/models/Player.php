<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_player".
 *
 * @property int $id 主键
 * @property string $name 球员姓名
 * @property int $nationality_team_id 国家队编号
 * @property string|null $birth_date 出生日期
 * @property string|null $position 位置
 * @property string|null $source_url 来源
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataTeam $nationalityTeam
 * @property WcDataEvent[] $wcDataEvents
 * @property WcDataSquad[] $wcDataSquads
 */
class Player extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_player';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['birth_date', 'position', 'source_url'], 'default', 'value' => null],
            [['name', 'nationality_team_id'], 'required'],
            [['nationality_team_id'], 'integer'],
            [['birth_date', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 160],
            [['position'], 'string', 'max' => 40],
            [['source_url'], 'string', 'max' => 1000],
            [['nationality_team_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTeam::class, 'targetAttribute' => ['nationality_team_id' => 'id']],
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
            'nationality_team_id' => 'Nationality Team ID',
            'birth_date' => 'Birth Date',
            'position' => 'Position',
            'source_url' => 'Source Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[NationalityTeam]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNationalityTeam()
    {
        return $this->hasOne(WcDataTeam::class, ['id' => 'nationality_team_id']);
    }

    /**
     * Gets query for [[WcDataEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataEvents()
    {
        return $this->hasMany(WcDataEvent::class, ['player_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataSquads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataSquads()
    {
        return $this->hasMany(WcDataSquad::class, ['player_id' => 'id']);
    }

}
