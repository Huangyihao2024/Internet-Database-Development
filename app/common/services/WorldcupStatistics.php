<?php
/** 世界杯数据库统计服务。负责人：王乐之（2411302）；AI辅助开发。 */
namespace common\services;
use Yii;
class WorldcupStatistics {
    /** 按2022决赛阶段规则计算小组积分和完整的可计算排序项。 */
    public static function standings(int $tournamentId): array {
        $rows=(new \yii\db\Query())->from(['m'=>'wc_data_match'])->innerJoin(['g'=>'wc_data_group'],'g.id=m.group_id')->where(['m.tournament_id'=>$tournamentId,'m.status'=>'finished'])->all();
        $table=[];
        foreach ($rows as $m) foreach (['home','away'] as $side) {
            $team=(int)$m[$side.'_team_id'];$op=$side==='home'?'away':'home';$gf=(int)$m['normal_'.$side];$ga=(int)$m['normal_'.$op];
            $key=$m['group_id'].':'.$team;
            $table[$key]??=['group_id'=>(int)$m['group_id'],'team_id'=>$team,'played'=>0,'win'=>0,'draw'=>0,'loss'=>0,'gf'=>0,'ga'=>0,'points'=>0];
            $table[$key]['played']++;$table[$key]['gf']+=$gf;$table[$key]['ga']+=$ga;
            if ($gf>$ga) {$table[$key]['win']++;$table[$key]['points']+=3;}
            elseif ($gf===$ga) {$table[$key]['draw']++;$table[$key]['points']++;}
            else $table[$key]['loss']++;
        }
        $cards=(new \yii\db\Query())->select(['e.id','e.match_id','e.player_id','e.team_id','e.type'])->from(['e'=>'wc_data_event'])
            ->innerJoin(['m'=>'wc_data_match'],'m.id=e.match_id')
            ->where(['m.tournament_id'=>$tournamentId,'m.status'=>'finished','e.type'=>['yellow_card','red_card']])
            ->andWhere(['not',['m.group_id'=>null]])->all();
        $discipline=[];
        foreach ($cards as $card) {
            $key=$card['match_id'].':'.($card['player_id']??'event-'.$card['id']);
            $discipline[$key]['team_id']=(int)$card['team_id'];
            $discipline[$key][$card['type']]=($discipline[$key][$card['type']]??0)+1;
        }
        $fair=[];
        foreach ($discipline as $entry) {
            $yellow=$entry['yellow_card']??0;$red=$entry['red_card']??0;
            // 同场同球员仅使用一次扣分；现有事件未区分两黄变红与黄牌后直接红牌。
            $deduction=$red?($yellow?3:4):$yellow;
            $fair[$entry['team_id']]=($fair[$entry['team_id']]??0)-$deduction;
        }
        $result=array_values($table);
        foreach ($result as &$row) {$row['fair_play']=$fair[$row['team_id']]??0;$row['tie_break_pending']=false;}
        unset($row);
        usort($result,fn($a,$b)=>[$a['group_id'],-$a['points'],-($a['gf']-$a['ga']),-$a['gf']]<=>[$b['group_id'],-$b['points'],-($b['gf']-$b['ga']),-$b['gf']]);
        // 只在总积分、总净胜球及总进球相同的队伍之间计算相互战绩。
        for ($start=0,$count=count($result);$start<$count;$start=$end) {
            $end=$start+1;
            while ($end<$count && self::sameOverall($result[$start],$result[$end])) $end++;
            if ($end-$start<2) continue;
            $ids=array_column(array_slice($result,$start,$end-$start),'team_id');
            $head=[];
            foreach ($ids as $id) $head[$id]=['points'=>0,'gd'=>0,'gf'=>0];
            foreach ($rows as $match) {
                $home=(int)$match['home_team_id'];$away=(int)$match['away_team_id'];
                if ((int)$match['group_id']!==(int)$result[$start]['group_id'] || !isset($head[$home],$head[$away])) continue;
                $h=(int)$match['normal_home'];$a=(int)$match['normal_away'];
                $head[$home]['points']+=($h>$a?3:($h===$a?1:0));
                $head[$away]['points']+=($a>$h?3:($h===$a?1:0));
                $head[$home]['gd']+=$h-$a;$head[$away]['gd']+=$a-$h;
                $head[$home]['gf']+=$h;$head[$away]['gf']+=$a;
            }
            $slice=array_slice($result,$start,$end-$start);
            usort($slice,static function ($a,$b) use ($head) {
                $x=$head[$a['team_id']];$y=$head[$b['team_id']];
                return [-$x['points'],-$x['gd'],-$x['gf'],-$a['fair_play'],$a['team_id']]
                    <=>[-$y['points'],-$y['gd'],-$y['gf'],-$b['fair_play'],$b['team_id']];
            });
            foreach ($slice as $offset=>$row) {
                $rank=[$head[$row['team_id']]['points'],$head[$row['team_id']]['gd'],$head[$row['team_id']]['gf'],$row['fair_play']];
                foreach ($slice as $other) if ($other['team_id']!==$row['team_id'] &&
                    $rank===[$head[$other['team_id']]['points'],$head[$other['team_id']]['gd'],$head[$other['team_id']]['gf'],$other['fair_play']]) {
                    $row['tie_break_pending']=true;break;
                }
                $result[$start+$offset]=$row;
            }
        }
        return $result;
    }
    /** 判断两队是否需要进入相互战绩比较。 */
    private static function sameOverall(array $a,array $b): bool {
        return $a['group_id']===$b['group_id'] && $a['points']===$b['points']
            && $a['gf']-$a['ga']===$b['gf']-$b['ga'] && $a['gf']===$b['gf'];
    }
    /** 射手榜汇总正式进球及比赛内点球，不计乌龙球与点球大战。 */
    public static function scorers(int $tournamentId,int $limit=100): array {
        return (new \yii\db\Query())->select(['e.player_id','p.name','t.name AS team_name','goals'=>'COUNT(*)','penalties'=>"SUM(e.type='penalty_goal')"])
            ->from(['e'=>'wc_data_event'])->innerJoin(['m'=>'wc_data_match'],'m.id=e.match_id')->innerJoin(['p'=>'wc_data_player'],'p.id=e.player_id')->innerJoin(['t'=>'wc_data_team'],'t.id=e.team_id')
            ->where(['m.tournament_id'=>$tournamentId,'m.status'=>'finished','e.type'=>['goal','penalty_goal']])->groupBy(['e.player_id','p.name','t.name'])->orderBy(['goals'=>SORT_DESC,'penalties'=>SORT_ASC,'p.name'=>SORT_ASC])->limit($limit)->all();
    }
    /** 根据届次、阶段和球队筛选比赛，供四类图表复用。 */
    public static function chartMatches(int $tournamentId,?int $stageId,?int $teamId): array {
        $q=(new \yii\db\Query())->from(['m'=>'wc_data_match'])->where(['m.tournament_id'=>$tournamentId,'m.status'=>'finished']);
        if ($stageId) $q->andWhere(['m.stage_id'=>$stageId]);
        if ($teamId) $q->andWhere(['or',['m.home_team_id'=>$teamId],['m.away_team_id'=>$teamId]]);
        return $q->orderBy(['m.kicks_off_at'=>SORT_ASC,'m.id'=>SORT_ASC])->all();
    }
    /** 聚合四类图表所需的即时数据和明确的统计口径。 */
    public static function charts(int $tournamentId,?int $stageId,?int $teamId): array {
        $matches=self::chartMatches($tournamentId,$stageId,$teamId);$teams=[];$trend=[];$stages=[];$ids=[];
        foreach ($matches as $m) {
            $ids[]=$m['id'];$total=0;
            foreach (['home','away'] as $side) {
                $goals=(int)$m['normal_'.$side]+(int)$m['extra_'.$side];$total+=$goals;$id=$m[$side.'_team_id'];$teams[$id]=($teams[$id]??0)+$goals;
            }
            $trend[]=['date'=>substr($m['kicks_off_at'],0,10),'match_number'=>(int)$m['match_number'],'goals'=>$total];
            $stages[$m['stage_id']]=($stages[$m['stage_id']]??0)+$total;
        }
        $names=(new \yii\db\Query())->select(['id','name'])->from('wc_data_team')->indexBy('id')->all();
        $stageNames=(new \yii\db\Query())->select(['id','name'])->from('wc_data_stage')->indexBy('id')->all();
        $players=[];
        if ($ids) {$playerQuery=(new \yii\db\Query())->select(['p.name','goals'=>'COUNT(*)'])->from(['e'=>'wc_data_event'])->innerJoin(['p'=>'wc_data_player'],'p.id=e.player_id')->where(['e.match_id'=>$ids,'e.type'=>['goal','penalty_goal']]);if ($teamId)$playerQuery->andWhere(['e.team_id'=>$teamId]);$players=$playerQuery->groupBy(['e.player_id','p.name'])->orderBy(['goals'=>SORT_DESC,'p.name'=>SORT_ASC])->limit(12)->all();}
        return ['scope'=>'正式进球含加时，不含点球大战；球员进球不含乌龙球','teams'=>array_map(fn($id,$goals)=>['name'=>$names[$id]['name']??$id,'goals'=>$goals],array_keys($teams),array_values($teams)),'trend'=>$trend,'stages'=>array_map(fn($id,$goals)=>['name'=>$stageNames[$id]['name']??$id,'goals'=>$goals],array_keys($stages),array_values($stages)),'players'=>$players];
    }
}
