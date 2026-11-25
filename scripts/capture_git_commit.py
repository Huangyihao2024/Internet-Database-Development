"""Capture the actual public GitHub commit page."""
from pathlib import Path
from playwright.sync_api import sync_playwright
url='https://github.com/Huangyihao2024/Internet-Database-Development/commit/950c5ae7289dfe41ff76581efb95c316a50c6b33'
out=Path(__file__).resolve().parents[1]/'figures/34_git_commit.png'
with sync_playwright() as playwright:
    browser=playwright.chromium.launch(channel='msedge',headless=True)
    page=browser.new_page(viewport={'width':1440,'height':900},device_scale_factor=1)
    response=page.goto(url,wait_until='domcontentloaded',timeout=30000)
    assert response.status==200
    page.wait_for_timeout(1500)
    assert '950c5ae' in page.content()
    page.screenshot(path=str(out))
    browser.close()
print(out)
