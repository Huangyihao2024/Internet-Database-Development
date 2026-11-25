# -*- coding: utf-8 -*-
"""源数据导入前关键校验；负责人王乐之（2411302），AI辅助开发。"""
import collections
import hashlib
import json
from pathlib import Path
root=Path(__file__).resolve().parents[1]
data=json.loads((root/'data/raw/worldcup_2022_parsed.json').read_text(encoding='utf-8'))
sources=json.loads((root/'data/raw/sources.json').read_text(encoding='utf-8'))
for source in sources:
    path=root/'data/raw'/source['file']
    assert path.stat().st_size==source['bytes']
    assert hashlib.sha256(path.read_bytes()).hexdigest()==source['sha256']
matches=data['matches'];teams=set(sum(data['groups'].values(),[]))
assert len(matches)==64 and len(teams)==32
assert len({(m['date'],m['home'],m['away']) for m in matches})==64
assert len({m['stadium'] for m in matches})==8
assert sum(m['normal_home']+m['normal_away']+(m['extra_home'] or 0)+(m['extra_away'] or 0) for m in matches)==172
assert sum(e['type'] in ('goal','penalty_goal','own_goal') for m in matches for e in m['events'])==172
for m in matches:
    for side in ('home','away'):
        team=m[side]
        actual=sum(e['team']==team and e['type'] in ('goal','penalty_goal','own_goal') for e in m['events'])
        assert actual==m['normal_'+side]+(m['extra_'+side] or 0),(m['source_line'],team)
points=collections.defaultdict(int);goals=collections.Counter()
table=collections.defaultdict(lambda:[0,0,0])
for m in matches:
    if m['stage']=='Group':
        h,a=m['normal_home'],m['normal_away']
        points[m['home']]+=3 if h>a else 1 if h==a else 0
        points[m['away']]+=3 if a>h else 1 if h==a else 0
        for team,own,opp in ((m['home'],h,a),(m['away'],a,h)):
            row=table[(m['group'],team)];row[0]+=3 if own>opp else 1 if own==opp else 0;row[1]+=own-opp;row[2]+=own
    for e in m['events']:
        if e['type'] in ('goal','penalty_goal'):goals[e['player']]+=1
assert points['Netherlands']==7 and points['England']==7 and points['Argentina']==6
expected={'A':['Netherlands','Senegal'],'B':['England','USA'],'C':['Argentina','Poland'],'D':['France','Australia'],'E':['Japan','Spain'],'F':['Morocco','Croatia'],'G':['Brazil','Switzerland'],'H':['Portugal','South Korea']}
for group,leaders in expected.items():
    ranked=sorted((team for g,team in table if g==group),key=lambda team:tuple(-x for x in table[(group,team)]))
    assert ranked[:2]==leaders,(group,ranked)
assert goals['Kylian Mbappe']==8 and goals['Lionel Messi']==7
assert sum(e['type']=='shootout_goal' for m in matches for e in m['events'])==26
print('PASS: 64 matches, 32 teams, 8 stadiums, 172 goals; group points and top scorers checked')
