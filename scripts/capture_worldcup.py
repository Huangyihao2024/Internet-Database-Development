"""Capture actual running pages with installed Microsoft Edge."""
import json
import pathlib
from playwright.sync_api import sync_playwright

root=pathlib.Path(__file__).resolve().parents[1]
out=root/'figures'
accounts={x['username']:x for x in json.loads((root/'.local/accounts.json').read_text(encoding='utf-8'))}
front='http://127.0.0.1:8781/index.php?r='
back='http://127.0.0.1:8782/index.php?r='

with sync_playwright() as playwright:
    browser=playwright.chromium.launch(channel='msedge',headless=True)
    context=browser.new_context(viewport={'width':1440,'height':900},device_scale_factor=1)
    page=context.new_page()
    pages=[('19_matches.png','match/index'),('20_match_detail.png','match/view&id=64'),('21_teams.png','team/index'),('22_team_detail.png','team/view&id=1'),('23_players.png','player/index'),('24_player_detail.png','player/view&id=1'),('25_standings.png','standing/index'),('27_charts.png','statistics/index')]
    for filename,route in pages:
        response=page.goto(front+route,wait_until='networkidle')
        assert response.status==200,(route,response.status)
        if route=='statistics/index': page.locator('#wc-chart-status').wait_for()
        page.screenshot(path=str(out/filename),full_page=True)
        print(filename,response.status)
    page.goto(front+'statistics/index',wait_until='networkidle')
    page.locator('select[name="stage_id"]').select_option('1')
    page.locator('select[name="team_id"]').select_option('1')
    page.wait_for_timeout(600)
    assert page.locator('#wc-trend svg').count()==1
    page.screenshot(path=str(out/'32_charts_filtered.png'),full_page=True)
    page.goto(front+'standing/index',wait_until='networkidle')
    page.get_by_text('射手榜',exact=True).scroll_into_view_if_needed()
    page.screenshot(path=str(out/'26_scorers.png'))
    page.goto(front+'site/login',wait_until='networkidle')
    page.locator('input[name="LoginForm[username]"]').fill(accounts['wanglezhi']['username'])
    page.locator('input[name="LoginForm[password]"]').fill(accounts['wanglezhi']['password'])
    page.locator('form button').first.click()
    page.wait_for_timeout(1500)
    print('front login result',page.url,page.locator('.help-block,.invalid-feedback,.alert-error').all_text_contents())
    page.goto(front+'favorite/index',wait_until='networkidle')
    page.screenshot(path=str(out/'28_favorites.png'),full_page=True)
    admin=browser.new_context(viewport={'width':1440,'height':900},device_scale_factor=1).new_page()
    admin.goto(back+'site/login',wait_until='networkidle')
    admin.locator('input[name="LoginForm[username]"]').fill(accounts['huangyihao']['username'])
    admin.locator('input[name="LoginForm[password]"]').fill(accounts['huangyihao']['password'])
    admin.locator('form button').first.click()
    admin.wait_for_timeout(1500)
    print('backend login result',admin.url,admin.locator('.help-block,.invalid-feedback,.alert-error').all_text_contents())
    for filename,route in [('29_admin_data.png','data/index&type=match'),('30_admin_import.png','import/index')]:
        response=admin.goto(back+route,wait_until='networkidle')
        assert response.status==200,(route,response.status)
        admin.screenshot(path=str(out/filename),full_page=True)
        print(filename,response.status)
    response=admin.goto(back+'gii',wait_until='networkidle')
    assert response.status==200 and 'site/login' not in admin.url
    admin.screenshot(path=str(out/'33_gii.png'),full_page=True)
    mobile=browser.new_context(viewport={'width':390,'height':844},device_scale_factor=1,is_mobile=True,has_touch=True).new_page()
    mobile.goto(front+'statistics/index',wait_until='networkidle')
    mobile.screenshot(path=str(out/'31_charts_mobile.png'),full_page=True)
    browser.close()
