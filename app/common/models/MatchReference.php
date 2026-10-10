<?php
/** 社区比赛关联的只读选项；本人负责，AI辅助编写。 */
namespace common\models;
class MatchReference extends \yii\db\ActiveRecord {
 public static function tableName(){return 'wc_data_match';}
 public static function options(){
  $rows=self::find()->alias('m')->select(['m.id','label'=>new \yii\db\Expression("CONCAT(h.name, ' vs ', a.name)")])->leftJoin('wc_data_team h','h.id=m.home_team_id')->leftJoin('wc_data_team a','a.id=m.away_team_id')->orderBy(['m.kicks_off_at'=>SORT_DESC])->limit(200)->asArray()->all();
  return \yii\helpers\ArrayHelper::map($rows,'id','label');
 }
}
