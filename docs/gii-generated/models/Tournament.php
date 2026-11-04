<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_tournament".
 *
 * @property int $id 主键
 * @property int $year 年份
 * @property string $name 赛事名称
 * @property string $starts_at 开始日期
 * @property string $ends_at 结束日期
 * @property string|null $source_url 来源
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataGroup[] $wcDataGroups
 * @property WcDataMatch[] $wcDataMatches
 * @property WcDataSquad[] $wcDataSquads
 * @property WcDataStage[] $wcDataStages
 */
class Tournament extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_tournament';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['source_url'], 'default', 'value' => null],
            [['year', 'name', 'starts_at', 'ends_at'], 'required'],
            [['year'], 'integer'],
            [['starts_at', 'ends_at', 'created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 160],
            [['source_url'], 'string', 'max' => 1000],
            [['year'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'year' => 'Year',
            'name' => 'Name',
            'starts_at' => 'Starts At',
            'ends_at' => 'Ends At',
            'source_url' => 'Source Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[WcDataGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataGroups()
    {
        return $this->hasMany(WcDataGroup::class, ['tournament_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataMatches()
    {
        return $this->hasMany(WcDataMatch::class, ['tournament_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataSquads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataSquads()
    {
        return $this->hasMany(WcDataSquad::class, ['tournament_id' => 'id']);
    }

    /**
     * Gets query for [[WcDataStages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataStages()
    {
        return $this->hasMany(WcDataStage::class, ['tournament_id' => 'id']);
    }

}
