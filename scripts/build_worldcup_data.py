"""解析仓库中的 OpenFootball 2022 原文，负责人王乐之（2411302），AI辅助开发。"""
import hashlib
import json
import re
import csv
import difflib
import unicodedata
from datetime import datetime, timedelta, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
RAW = ROOT / 'data' / 'raw'
GROUPS = {}
matches = []
warnings = []
stadiums = {}
stage = 'Group'
group = None
day = None
last_time = None
month = {'Nov': 11, 'Dec': 12}
name_re = re.compile(r'^\s*(?:(\d{1,2}:\d{2})\s+)?(.+?)\s+(\d+)-(\d+)\s*(.*?)\s+@\s+(.+?),\s*(.+?)\s*$')

for filename in ['worldcup_2022.txt', 'worldcup_2022_finals.txt']:
    lines = (RAW / filename).read_text(encoding='utf-8').splitlines()
    if not GROUPS:
        for group_line in lines:
            initial = re.match(r'^Group ([A-H])\s*\|\s*(.*)', group_line)
            if initial:
                GROUPS[initial[1]] = re.split(r'\s{2,}', initial[2].strip())
    for lineno, line in enumerate(lines, 1):
        g = re.match(r'^Group ([A-H])\s*\|\s*(.*)', line)
        if g:
            GROUPS[g[1]] = re.split(r'\s{2,}', g[2].strip())
        h = re.match(r'^▪\s+(Group ([A-H])|Round of 16|Quarter-finals|Semi-finals|Match for third place|Final)\s*$', line)
        if h:
            stage = 'Group' if h[2] else h[1]
            group = h[2]
        d = re.match(r'^(?:Sun|Mon|Tue|Wed|Thu|Fri|Sat)\s+(Nov|Dec)\s+(\d{1,2})\s*$', line.strip())
        if d:
            day = f'2022-{month[d[1]]:02d}-{int(d[2]):02d}'
            last_time = None
        m = name_re.match(line)
        if not m:
            continue
        clock, home, hs, as_, score, venue, city = m.groups()
        # 球队名从分组名单判定，避免将比分后的淘汰赛注记识别为球队。
        candidates = set(sum(GROUPS.values(), []))
        known_away = None
        for candidate in sorted(candidates, key=len, reverse=True):
            if score.endswith(candidate):
                known_away = candidate
                score = score[:-len(candidate)].strip()
                break
        if known_away is None:
            warnings.append(f'{filename}:{lineno} 无法识别客队: {line.strip()}')
            continue
        away = known_away
        home = home.strip()
        if home not in candidates:
            warnings.append(f'{filename}:{lineno} 无法识别主队: {home}')
            continue
        score = score.strip()
        norm_h, norm_a = int(hs), int(as_)
        extra_h = extra_a = penalty_h = penalty_a = None
        if 'a.e.t' in score:
            q = re.search(r'\((\d+)-(\d+),\s*(\d+)-(\d+)\)', score)
            if not q:
                warnings.append(f'{filename}:{lineno} 加时比分无法解析: {score}')
                continue
            norm_h, norm_a = int(q[1]), int(q[2])
            extra_h, extra_a = int(hs)-norm_h, int(as_)-norm_a
            p = re.search(r'(\d+)-(\d+)\s+pen\.', score)
            if p:
                penalty_h, penalty_a = int(p[1]), int(p[2])
        if not clock:
            clock = last_time
            warnings.append(f'{filename}:{lineno} 缺少开球时间，沿用同日上一场时间 {clock}')
        if not day or not clock:
            warnings.append(f'{filename}:{lineno} 缺少日期或时间')
            continue
        last_time = clock
        record = {'stage': stage, 'group': group, 'date': day, 'time': clock,
                  'home': home, 'away': away, 'normal_home': norm_h, 'normal_away': norm_a,
                  'extra_home': extra_h, 'extra_away': extra_a,
                  'penalty_home': penalty_h, 'penalty_away': penalty_a,
                  'stadium': venue.strip(), 'city': city, 'source_line': f'{filename}:{lineno}'}
        matches.append(record)

output = {'source': 'OpenFootball worldcup 2022 cup.txt + cup_finals.txt', 'groups': GROUPS,
          'matches': matches, 'warnings': warnings}
full = json.loads((RAW / 'worldcup_2022_full.json').read_text(encoding='utf-8'))['matches']
full_index = {(m['date'],m['team1'],m['team2']):m for m in full}
for m in matches:
    detail = full_index.get((m['date'],m['home'],m['away']))
    if detail is None:
        raise ValueError('详细事件与原始赛程无法关联：' + m['source_line'])
    actual_score = detail['score']['et'] if isinstance(detail['score'],dict) else detail['score']
    if actual_score != [m['normal_home']+(m['extra_home'] or 0),m['normal_away']+(m['extra_away'] or 0)]:
        raise ValueError('详细事件与原始比分不符：' + m['source_line'])
    m['events'] = []
    for side, team in enumerate((m['home'],m['away']), 1):
        for goal in detail.get('goals'+str(side), []):
            raw_minute=str(goal['minute']).split('+')
            m['events'].append({'team':team,'player':goal['name'],'minute':int(raw_minute[0]),
                                'added_minute':int(raw_minute[1]) if len(raw_minute)>1 else 0,
                                'type':'own_goal' if goal.get('owngoal') else ('penalty_goal' if goal.get('penalty') else 'goal')})
        for card in detail.get('bookings',[[],[]])[side-1]:
            raw_minute=str(card['minute']).split('+')
            m['events'].append({'team':team,'player':card['name'],'minute':int(raw_minute[0]),
                                'added_minute':int(raw_minute[1]) if len(raw_minute)>1 else 0,
                                'type':'red_card' if card['type'] in ('R','Y/R') else 'yellow_card'})
    for shootout_index, kick in enumerate(detail.get('penalties',[]),1):
        m['events'].append({'team':None,'player':kick['name'],'minute':120,'added_minute':shootout_index,
                            'type':'shootout_miss' if kick.get('note')=='missed' else 'shootout_goal'})
def normalize(name):
    return re.sub('[^a-z]','',unicodedata.normalize('NFKD',name).encode('ascii','ignore').decode().lower())
with (RAW/'players_2022_stiles.csv').open(encoding='utf-8',newline='') as stream:
    roster=list(csv.DictReader(stream))
aliases={'Iran':'IR Iran','USA':'United States','South Korea':'Korea Republic'}
rosters={team:[p['name'] for p in roster if p['team_name']==aliases.get(team,team)] for team in set(sum(GROUPS.values(),[]))}
unmatched=[]
for m in matches:
    for event in m['events']:
        team=event['team']
        if team is None:
            source=normalize(event['player'])
            matched=[t for t,names in rosters.items() if t in (m['home'],m['away']) and any(normalize(p)==source for p in names)]
            if len(matched)!=1: raise ValueError('点球大战球员队伍无法确认：'+event['player'])
            event['team']=team=matched[0]
        if event['type']=='own_goal':
            team=m['away'] if team==m['home'] else m['home']
        source=normalize(event['player'])
        candidates=rosters[team]
        exact=[p for p in candidates if normalize(p)==source]
        partial=[p for p in candidates if source in normalize(p) or normalize(p) in source]
        if len(exact)==1: chosen=exact[0]
        elif len(partial)==1: chosen=partial[0]
        else:
            ranked=sorted(((difflib.SequenceMatcher(None,source,normalize(p)).ratio(),p) for p in candidates),reverse=True)
            chosen=ranked[0][1] if ranked[0][0]>=.78 and ranked[0][0]-ranked[1][0]>=.12 else None
        if chosen: event['player']=chosen
        else: unmatched.append((m['source_line'],event['player'],team))
warnings.extend('未唯一匹配球员：%s %s (%s)'%item for item in unmatched)
(RAW / 'worldcup_2022_parsed.json').write_text(json.dumps(output, ensure_ascii=False, indent=2), encoding='utf-8')
print(json.dumps({'matches':len(matches),'teams':len(set(sum(GROUPS.values(),[]))),'warnings':warnings},ensure_ascii=False,indent=2))
