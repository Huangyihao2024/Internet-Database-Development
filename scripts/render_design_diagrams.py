# -*- coding: utf-8 -*-
"""将可编辑 Mermaid 图源渲染为 SVG/PNG；负责人王乐之（2411302），AI辅助开发。"""
from html import escape
from pathlib import Path
from urllib.request import urlopen
from playwright.sync_api import sync_playwright

root = Path(__file__).resolve().parents[1]
cache = root / '.local' / 'mermaid.min.js'
cache.parent.mkdir(exist_ok=True)


def load_mermaid():
    """读取本机缓存；首次运行下载 Mermaid 浏览器发行包。"""
    if not cache.exists():
        cache.write_bytes(urlopen('https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js', timeout=30).read())
    return cache.read_text(encoding='utf-8')


def render_one(page, source):
    """按原始 mmd 文件生成同名可缩放 SVG 和浏览器截图 PNG。"""
    content = source.read_text(encoding='utf-8')
    page.set_content('<html><head><meta charset="utf-8"><style>body{margin:24px;background:#fff}.mermaid{display:inline-block}</style></head><body><div class="mermaid">'+escape(content)+'</div></body></html>')
    page.add_script_tag(content=load_mermaid())
    page.evaluate("async () => {mermaid.initialize({startOnLoad:false,theme:'default',securityLevel:'strict'});await mermaid.run({querySelector:'.mermaid'});}")
    svg = page.locator('.mermaid svg')
    svg.wait_for()
    svg.evaluate("element => {const width=element.viewBox.baseVal.width;element.setAttribute('width',String(width));element.style.width=width+'px';element.style.maxWidth='none';}")
    source.with_suffix('.svg').write_text(svg.evaluate('(element) => element.outerHTML'), encoding='utf-8')
    svg.screenshot(path=str(source.with_suffix('.png')))
    print(source.name, svg.bounding_box())


with sync_playwright() as playwright:
    browser = playwright.chromium.launch(channel='msedge', headless=True)
    page = browser.new_page(viewport={'width': 1600, 'height': 1200}, device_scale_factor=1)
    for source in sorted((root / 'figures').glob('*.mmd')):
        render_one(page, source)
    browser.close()
