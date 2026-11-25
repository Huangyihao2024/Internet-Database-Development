<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_stadium".
 *
 * @property int $id 主键
 * @property string $name 球场名称
 * @property string $city 城市
 * @property int|null $capacity 容量
 * @property string|null $source_url 来源
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataMatch[] $wcDataMatches
 */
class Stadium extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_stadium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['capacity', 'source_url'], 'default', 'value' => null],
            [['name', 'city'], 'required'],
            [['capacity'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 160],
            [['city'], 'string', 'max' => 100],
            [['source_url'], 'string', 'max' => 1000],
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
            'city' => 'City',
            'capacity' => 'Capacity',
            'source_url' => 'Source Url',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[WcDataMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataMatches()
    {
        return $this->hasMany(WcDataMatch::class, ['stadium_id' => 'id']);
    }

}
