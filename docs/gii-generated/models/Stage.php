<?php

namespace common\models\gii;

use Yii;

/**
 * This is the model class for table "wc_data_stage".
 *
 * @property int $id 主键
 * @property int $tournament_id 届次编号
 * @property string $name 阶段名称
 * @property int $sort_order 展示顺序
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 *
 * @property WcDataTournament $tournament
 * @property WcDataMatch[] $wcDataMatches
 */
class Stage extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wc_data_stage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tournament_id', 'name', 'sort_order'], 'required'],
            [['tournament_id', 'sort_order'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 80],
            [['tournament_id', 'name'], 'unique', 'targetAttribute' => ['tournament_id', 'name']],
            [['tournament_id'], 'exist', 'skipOnError' => true, 'targetClass' => WcDataTournament::class, 'targetAttribute' => ['tournament_id' => 'id']],
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
            'name' => 'Name',
            'sort_order' => 'Sort Order',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
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
     * Gets query for [[WcDataMatches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWcDataMatches()
    {
        return $this->hasMany(WcDataMatch::class, ['stage_id' => 'id']);
    }

}
