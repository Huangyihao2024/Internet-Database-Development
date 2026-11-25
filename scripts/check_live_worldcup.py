"""Local HTTP acceptance checks against the real Yii and MySQL service."""
import json
import pathlib
import requests
from bs4 import BeautifulSoup

root=pathlib.Path(__file__).resolve().parents[1]
accounts={x['username']:x for x in json.loads((root/'.local/accounts.json').read_text(encoding='utf-8'))}
front='http://127.0.0.1:8781/index.php?r='
back='http://127.0.0.1:8782/index.php?r='

def csrf(html):
    node=BeautifulSoup(html,'html.parser').find('input',{'name':lambda x:x and x.startswith('_csrf-')})
    assert node, 'CSRF field absent'
    return node['name'],node['value']

def login(base,account):
    session=requests.Session()
    page=session.get(base+'site/login',timeout=10)
    key,value=csrf(page.text)
    response=session.post(base+'site/login',data={key:value,'LoginForm[username]':account['username'],'LoginForm[password]':account['password']},timeout=10)
    assert 'login' not in response.url.lower(),response.url
    return session

fan=login(front,accounts['wanglezhi'])
page=fan.get(front+'team/view&id=1',timeout=10)
key,value=csrf(page.text)
assert fan.post(front+'favorite/toggle&type=team&id=1',data={key:value},timeout=10).status_code==200
page=fan.get(front+'match/view&id=1',timeout=10)
key,value=csrf(page.text)
assert fan.post(front+'favorite/toggle&type=match&id=1',data={key:value},timeout=10).status_code==200
favorites=fan.get(front+'favorite/index',timeout=10)
assert '我的收藏' in favorites.text
sections=BeautifulSoup(favorites.text,'html.parser')
team_section=sections.find('h2',string='球队').find_next_sibling('div',class_='grid')
match_section=sections.find('h2',string='比赛').find_next_sibling('div',class_='grid')
if 'Qatar' not in team_section.get_text():
    page=fan.get(front+'team/view&id=1',timeout=10)
    key,value=csrf(page.text)
    fan.post(front+'favorite/toggle&type=team&id=1',data={key:value},timeout=10)
if 'Qatar vs Ecuador' not in match_section.get_text():
    page=fan.get(front+'match/view&id=1',timeout=10)
    key,value=csrf(page.text)
    fan.post(front+'favorite/toggle&type=match&id=1',data={key:value},timeout=10)
favorites=fan.get(front+'favorite/index',timeout=10)
assert 'Qatar vs Ecuador' in favorites.text
print('PASS login and team/match favorites')

admin=login(back,accounts['huangyihao'])
page=admin.get(back+'data/index&type=stadium',timeout=10)
assert page.status_code==200 and '赛事数据维护' in page.text
page=admin.get(back+'import/index',timeout=10)
assert page.status_code==200 and '64' in page.text
key,value=csrf(page.text)
result=admin.post(back+'import/run',data={key:value},timeout=20)
assert result.status_code==200 and '导入完成' in result.text
print('PASS admin maintenance, preview, repeat import')

# Create, edit, and remove a temporary stadium through the backend forms.
page=admin.get(back+'data/edit&type=stadium',timeout=10)
key,value=csrf(page.text)
result=admin.post(back+'data/edit&type=stadium',data={key:value,'fields[name]':'验收临时球场','fields[city]':'测试城市'},timeout=10)
assert result.status_code==200 and '记录已保存' in result.text
table=BeautifulSoup(result.text,'html.parser')
row=next(tr for tr in table.find_all('tr') if '验收临时球场' in tr.get_text())
stadium_id=int(row.find('td').get_text(strip=True))
page=admin.get(back+f'data/edit&type=stadium&id={stadium_id}',timeout=10)
key,value=csrf(page.text)
result=admin.post(back+f'data/edit&type=stadium&id={stadium_id}',data={key:value,'fields[name]':'验收临时球场','fields[city]':'更新城市'},timeout=10)
assert result.status_code==200 and '记录已保存' in result.text
page=admin.get(back+'data/index&type=stadium',timeout=10)
key,value=csrf(page.text)
result=admin.post(back+f'data/delete&type=stadium&id={stadium_id}',data={key:value},timeout=10)
assert result.status_code==200 and '记录已删除' in result.text
print('PASS admin stadium create/update/delete')

chart=requests.get(front+'statistics/data&tournament_id=1',timeout=10)
assert chart.status_code==200 and len(chart.json()['trend'])==64
filtered=requests.get(front+'statistics/data&tournament_id=1&stage_id=1&team_id=1',timeout=10)
assert filtered.status_code==200 and filtered.json()['trend']
invalid=requests.get(front+'statistics/data&tournament_id=1&stage_id=999999',timeout=10)
assert invalid.status_code==400
print('PASS chart data, filters and invalid-stage response')
