"""生成赛事模块初始文件；负责人王乐之（2411302），AI辅助开发。"""
from pathlib import Path
import re

root = Path(__file__).resolve().parents[1] / 'app'
schema=(root.parent/'data/install.sql').read_text(encoding='utf-8')
models = {
 'Tournament':('wc_data_tournament','year,name,starts_at,ends_at','year'),
 'Stage':('wc_data_stage','tournament_id,name,sort_order','tournament_id,name'),
 'TournamentGroup':('wc_data_group','tournament_id,name','tournament_id,name'),
 'Stadium':('wc_data_stadium','name,city','name,city'),
 'NationalTeam':('wc_data_team','name,fifa_code','fifa_code'),
 'Player':('wc_data_player','name,nationality_team_id','name,nationality_team_id'),
 'Squad':('wc_data_squad','tournament_id,team_id,player_id','tournament_id,team_id,player_id'),
 'FootballMatch':('wc_data_match','tournament_id,stage_id,stadium_id,match_number,home_team_id,away_team_id,kicks_off_at','tournament_id,match_number'),
 'MatchEvent':('wc_data_event','match_id,team_id,minute,type','source_key'),
 'TeamFavorite':('wc_fan_team_favorite','user_id,team_id','user_id,team_id'),
 'MatchFavorite':('wc_fan_match_favorite','user_id,match_id','user_id,match_id'),
}
relations = {
 'Tournament':{'stages':('Stage','tournament_id',True),'groups':('TournamentGroup','tournament_id',True),'matches':('FootballMatch','tournament_id',True)},
 'Stage':{'tournament':('Tournament','tournament_id',False),'matches':('FootballMatch','stage_id',True)},
 'TournamentGroup':{'tournament':('Tournament','tournament_id',False),'squads':('Squad','group_id',True)},
 'NationalTeam':{'players':('Player','nationality_team_id',True),'squads':('Squad','team_id',True)},
 'Player':{'team':('NationalTeam','nationality_team_id',False),'squads':('Squad','player_id',True),'events':('MatchEvent','player_id',True)},
 'Squad':{'team':('NationalTeam','team_id',False),'player':('Player','player_id',False),'tournament':('Tournament','tournament_id',False)},
 'FootballMatch':{'tournament':('Tournament','tournament_id',False),'stage':('Stage','stage_id',False),'group':('TournamentGroup','group_id',False),'stadium':('Stadium','stadium_id',False),'homeTeam':('NationalTeam','home_team_id',False),'awayTeam':('NationalTeam','away_team_id',False),'events':('MatchEvent','match_id',True)},
 'MatchEvent':{'match':('FootballMatch','match_id',False),'player':('Player','player_id',False),'team':('NationalTeam','team_id',False)},
 'TeamFavorite':{'team':('NationalTeam','team_id',False)},'MatchFavorite':{'match':('FootballMatch','match_id',False)},
}
for cls,(table,required,unique) in models.items():
    req = ','.join("'"+x+"'" for x in required.split(','))
    unq = ','.join("'"+x+"'" for x in unique.split(','))
    block=schema.split('CREATE TABLE `'+table+'` (',1)[1].split('\n)',1)[0]
    columns=re.findall(r'^  `([^`]+)`\s+([^\n]+)',block,re.M)
    editable=[name for name,_ in columns if name not in ('id','created_at','updated_at')]
    integers=[name for name,decl in columns if re.match(r'(?:bigint|smallint|int)',decl) and name not in ('id',)]
    dates=[name for name,decl in columns if decl.startswith(('date ','datetime ')) and name not in ('created_at','updated_at')]
    safe=','.join("'"+x+"'" for x in editable)
    ints=','.join("'"+x+"'" for x in integers)
    datefields=','.join("'"+x+"'" for x in dates)
    length_rules=''
    for name,decl in columns:
        length=re.match(r'(?:varchar|char)\((\d+)\)',decl)
        if length:length_rules+=f"        [['{name}'], 'string', 'max'=>{length[1]}],\n"
    extra_rules={
        'FootballMatch':"        [['status'], 'in', 'range'=>['scheduled','finished','postponed']],\n        [['away_team_id'], 'compare', 'compareAttribute'=>'home_team_id', 'operator'=>'!='],\n",
        'MatchEvent':"        [['type'], 'in', 'range'=>['goal','penalty_goal','own_goal','yellow_card','red_card','shootout_goal','shootout_miss']],\n",
        'Squad':"        [['shirt_number'], 'integer', 'min'=>1, 'max'=>99],\n",
    }.get(cls,'')
    rel = ''
    for name,(target,fk,many) in relations.get(cls,{}).items():
        method = 'hasMany' if many else 'hasOne'
        mapping = f"['{fk}'=>'id']" if many else f"['id'=>'{fk}']"
        order="->orderBy(['minute'=>SORT_ASC,'added_minute'=>SORT_ASC,'id'=>SORT_ASC])" if cls=='FootballMatch' and name=='events' else ''
        rel += f"    /** 获取{name}关联。 */\n    public function get{name[0].upper()+name[1:]}() {{ return $this->{method}({target}::class, {mapping}){order}; }}\n"
    code = f'''<?php
/**
 * {cls}：赛事数据模型。负责人：王乐之（2411302）；AI辅助开发。
 * 初始字段和关联按现有 SQL 结构生成，验证与业务规则已手工适配。
 */
namespace common\\models;
class {cls} extends \\yii\\db\\ActiveRecord {{
    /** 返回现有业务表名。 */
    public static function tableName() {{ return '{table}'; }}
    /** 验证必填、唯一及字段类型。 */
    public function rules() {{ return [
        [[{req}], 'required'],
        [[{unq}], 'unique', 'targetAttribute'=>[{unq}]],
        [[{safe}], 'safe'],
        [[{ints}], 'integer'],
        [[{datefields}], 'safe'],
{length_rules}{extra_rules}        [['id'], 'integer'],
        [['created_at','updated_at'], 'safe'],
    ]; }}
{rel}}}
'''
    path=root/'common/models'/f'{cls}.php';path.write_text(code,encoding='utf-8')
print('generated',len(models),'models')
