<?php
/** 2022 世界杯数据预览与事务导入。负责人：王乐之（2411302）；AI辅助开发。 */
namespace console\controllers;
use Yii;
use yii\console\Controller;

class WorldcupImportController extends Controller {
    private const ALIASES = ['Iran'=>'IR Iran','USA'=>'United States','South Korea'=>'Korea Republic'];
    /** 读取解析文件并检查数量、引用、比分和球员来源。 */
    private function input(): array {
        $root = dirname(__DIR__, 3);
        $data = json_decode(file_get_contents($root.'/data/raw/worldcup_2022_parsed.json'), true, 512, JSON_THROW_ON_ERROR);
        $players = array_map('str_getcsv', file($root.'/data/raw/players_2022_stiles.csv', FILE_IGNORE_NEW_LINES));
        $head = array_shift($players); $players = array_map(fn($row)=>array_combine($head,$row),$players);
        if (count($data['matches']) !== 64 || count($players) < 800 || count($data['groups']) !== 8) throw new \RuntimeException('源数据数量不完整');
        $teams = array_merge(...array_values($data['groups']));
        if (count(array_unique($teams)) !== 32) throw new \RuntimeException('球队名单应为32支');
        foreach ($data['matches'] as $m) {
            if (!in_array($m['home'],$teams,true) || !in_array($m['away'],$teams,true) || $m['home']===$m['away']) throw new \RuntimeException('对阵球队无效：'.$m['source_line']);
            foreach (['normal_home','normal_away','extra_home','extra_away','penalty_home','penalty_away'] as $field) if ($m[$field] !== null && ($m[$field]<0 || $m[$field]>20)) throw new \RuntimeException('比分无效：'.$m['source_line']);
            if (!preg_match('/^2022-(11|12)-\d\d$/',$m['date'])) throw new \RuntimeException('日期无效：'.$m['source_line']);
            foreach (['home','away'] as $side) {
                $team=$m[$side];$scored=count(array_filter($m['events'],fn($e)=>$e['team']===$team && in_array($e['type'],['goal','penalty_goal','own_goal'],true)));
                if ($scored!==$m['normal_'.$side]+($m['extra_'.$side]??0)) throw new \RuntimeException('进球事件与比分不一致：'.$m['source_line']);
            }
        }
        return [$data,$players];
    }
    /** 在写入前展示数据量和源文本警告。 */
    public function actionPreview(): int {
        [$data,$players]=$this->input();
        $this->stdout('比赛 '.count($data['matches']).'、球队 32、球员 '.count($players).'、球场 '.count(array_unique(array_column($data['matches'],'stadium')))."\n");
        foreach ($data['warnings'] as $warning) $this->stderr($warning."\n");
        return 0;
    }
    /** 事务中按自然键更新赛事数据，重复执行不会增加重复行。 */
    public function actionRun(): int {
        [$code,$message]=$this->runImport();
        if ($code===0) $this->stdout($message."\n"); else $this->stderr($message."\n");
        return $code;
    }
    /** 供命令行与后台共用的导入执行入口。 */
    public function runImport(): array {
        $tx=null;
        try {
            [$data,$players]=$this->input(); $db=Yii::$app->db; $tx=$db->beginTransaction();
            $t=$this->upsert('wc_data_tournament',['year'=>2022],['name'=>'FIFA World Cup Qatar 2022','starts_at'=>'2022-11-20','ends_at'=>'2022-12-18','source_url'=>'https://github.com/openfootball/worldcup/tree/master/2022--qatar']);
            $stages=[]; foreach (['Group','Round of 16','Quarter-finals','Semi-finals','Match for third place','Final'] as $i=>$name) $stages[$name]=$this->upsert('wc_data_stage',['tournament_id'=>$t,'name'=>$name],['sort_order'=>$i+1]);
            $groups=[];$teams=[];
            foreach ($data['groups'] as $letter=>$names) {
                $groups[$letter]=$this->upsert('wc_data_group',['tournament_id'=>$t,'name'=>$letter],[]);
                foreach ($names as $name) $teams[$name]=$this->upsert('wc_data_team',['fifa_code'=>$this->code($name,$players)],['name'=>$name]);
            }
            $stadiums=[];$matchIds=[];
            foreach ($data['matches'] as $i=>$m) {
                $venue=$m['stadium'].'|'.$m['city'];
                $stadiums[$venue]??=$this->upsert('wc_data_stadium',['name'=>$m['stadium'],'city'=>$m['city']],['source_url'=>'https://github.com/openfootball/worldcup/blob/master/2022--qatar/cup.txt']);
                $kick=(new \DateTimeImmutable($m['date'].' '.$m['time'],new \DateTimeZone('Asia/Qatar')))->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s');
                $values=['stage_id'=>$stages[$m['stage']],'group_id'=>$m['group']?$groups[$m['group']]:null,'stadium_id'=>$stadiums[$venue],'home_team_id'=>$teams[$m['home']],'away_team_id'=>$teams[$m['away']],'kicks_off_at'=>$kick,'status'=>'finished','source_key'=>'openfootball:2022:'.($i+1)];
                foreach (['normal_home','normal_away','extra_home','extra_away','penalty_home','penalty_away'] as $field) $values[$field]=$m[$field];
                $matchIds[]=$this->upsert('wc_data_match',['tournament_id'=>$t,'match_number'=>$i+1],$values);
            }
            $playerIds=[];
            foreach ($players as $row) {
                $name=array_search($row['team_name'],self::ALIASES,true) ?: $row['team_name'];
                if (!isset($teams[$name])) throw new \RuntimeException('球员球队无法映射：'.$row['team_name']);
                $player=$this->upsert('wc_data_player',['name'=>$row['name'],'nationality_team_id'=>$teams[$name]],['birth_date'=>$row['dob']?:null,'position'=>$row['position_desc']?:null,'source_url'=>'https://github.com/stiles/world-cup/blob/master/data/processed/2022/players.csv']);
                $playerIds[$name][$this->normalize($row['name'])]=$player;
                $letter=null;foreach ($data['groups'] as $g=>$names) if (in_array($name,$names,true)) {$letter=$g;break;}
                $this->upsert('wc_data_squad',['tournament_id'=>$t,'team_id'=>$teams[$name],'player_id'=>$player],['group_id'=>$groups[$letter],'shirt_number'=>$row['jersey_number']!==''?(int)$row['jersey_number']:null]);
            }
            $unmatched=[];$eventCount=0;
            foreach ($data['matches'] as $i=>$m) foreach ($m['events'] as $j=>$event) {
                $team=$event['team'];$playerTeam=$team;
                if ($event['type']==='own_goal') $playerTeam=$team===$m['home']?$m['away']:$m['home'];
                $player=$this->resolvePlayer($playerIds[$playerTeam],$event['player']);
                if (!$player) $unmatched[]=$m['source_line'].' '.$event['player'];
                $this->upsert('wc_data_event',['source_key'=>'openfootball:2022:'.($i+1).':'.($j+1)],['match_id'=>$matchIds[$i],'team_id'=>$teams[$team],'player_id'=>$player,'minute'=>$event['minute'],'added_minute'=>$event['added_minute'],'type'=>$event['type']]);
                $eventCount++;
            }
            if ($unmatched) throw new \RuntimeException('事件球员无法匹配：'.implode('；',array_slice($unmatched,0,12)).'，共'.count($unmatched).'项');
            $tx->commit();return [0,"导入完成：64场、32队、".count($players)."名球员、{$eventCount}项事件。"];
        } catch (\Throwable $e) {if ($tx!==null)$tx->rollBack();return [1,'导入回滚：'.$e->getMessage()];}
    }
    /** 按球队来源代码确定唯一标识。 */
    private function code(string $name,array $players): string {
        $source=self::ALIASES[$name]??$name;
        foreach ($players as $p) if ($p['team_name']===$source) return $p['country_code'];
        throw new \RuntimeException('无球队代码：'.$name);
    }
    /** 统一大小写和变音字符以对应不同来源的人名。 */
    private function normalize(string $name): string {
        $ascii=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$name);
        return strtolower(preg_replace('/[^a-z]/i','',$ascii ?: $name));
    }
    /** 匹配赛事名单，未能唯一确认的姓名留给预览报错。 */
    private function resolvePlayer(array $ids,string $sourceName): ?int {
        $needle=$this->normalize($sourceName);
        if (isset($ids[$needle])) return $ids[$needle];
        $found=[];
        foreach ($ids as $name=>$id) if (str_contains($name,$needle) || str_contains($needle,$name)) $found[]=$id;
        return count($found)===1 ? $found[0] : null;
    }
    /** 按 SQL 自然唯一键查询并插入或更新。 */
    private function upsert(string $table,array $key,array $values): int {
        $db=Yii::$app->db;$id=(new \yii\db\Query())->from($table)->select('id')->where($key)->scalar($db);
        if ($id) {if ($values) $db->createCommand()->update($table,$values,['id'=>$id])->execute();return (int)$id;}
        $db->createCommand()->insert($table,$key+$values)->execute();return (int)$db->getLastInsertID();
    }
}
