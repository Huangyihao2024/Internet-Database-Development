from http.server import ThreadingHTTPServer, SimpleHTTPRequestHandler
from pathlib import Path
import json, sqlite3, urllib.parse, datetime
ROOT = Path(__file__).resolve().parent
SITE = ROOT / '网站'
DB = ROOT / '运行数据/messages.sqlite'
DB.parent.mkdir(exist_ok=True)
with sqlite3.connect(DB) as c:
    c.execute('CREATE TABLE IF NOT EXISTS messages(id INTEGER PRIMARY KEY AUTOINCREMENT,name TEXT NOT NULL,content TEXT NOT NULL,created_at TEXT NOT NULL)')
RESOURCES = [dict(id=1,title='数据库 SQL 入门',category='数据库',description='理解查询、连接与聚合，完成课程练习。'),dict(id=2,title='jQuery 交互实践',category='前端',description='用事件绑定和 DOM 操作实现页面交互。'),dict(id=3,title='Git 协作指南',category='工具',description='记录代码变更，整理提交与分支。'),dict(id=4,title='数据库索引笔记',category='数据库',description='通过执行计划认识索引的作用。'),dict(id=5,title='响应式布局入门',category='前端',description='适配桌面与移动设备的页面布局。'),dict(id=6,title='浏览器开发者工具',category='工具',description='检查页面、请求和响应数据。')]
class Handler(SimpleHTTPRequestHandler):
    def __init__(self,*args,**kw): super().__init__(*args,directory=str(SITE),**kw)
    def json_response(self,status,payload):
        body=json.dumps(payload,ensure_ascii=False).encode()
        self.send_response(status); self.send_header('Content-Type','application/json; charset=utf-8'); self.send_header('Content-Length',str(len(body))); self.end_headers(); self.wfile.write(body)
    def do_GET(self):
        url=urllib.parse.urlsplit(self.path)
        if url.path=='/api/resources':
            cat=urllib.parse.parse_qs(url.query).get('category',['全部'])[0]
            self.json_response(200,dict(items=[r for r in RESOURCES if cat=='全部' or r['category']==cat])); return
        if url.path=='/api/messages':
            with sqlite3.connect(DB) as c:
                c.row_factory=sqlite3.Row; rows=[dict(x) for x in c.execute('SELECT * FROM messages ORDER BY id DESC LIMIT 50')]
            self.json_response(200,dict(items=rows)); return
        if url.path.startswith('/data/personal/'):
            name=Path(urllib.parse.unquote(url.path)).name
            p=ROOT.parent/'data/personal'/name
            if p.is_file() and p.suffix=='.zip':
                body=p.read_bytes(); self.send_response(200); self.send_header('Content-Type','application/zip'); self.send_header('Content-Length',str(len(body))); self.end_headers();self.wfile.write(body);return
            self.send_error(404);return
        super().do_GET()
    def do_POST(self):
        if self.path!='/api/messages':self.json_response(404,dict(error='接口不存在'));return
        try:
            n=int(self.headers.get('Content-Length',0))
            if n>4096: raise ValueError('请求过大')
            data=json.loads(self.rfile.read(n));name=data.get('name','').strip();content=data.get('content','').strip()
            if not name or not content or len(name)>30 or len(content)>500:raise ValueError('姓名或留言长度不合法')
        except (ValueError,AttributeError,TypeError):self.json_response(400,dict(error='姓名或留言不合法'));return
        now=datetime.datetime.now().isoformat(timespec='seconds')
        with sqlite3.connect(DB) as c:
            cur=c.execute('INSERT INTO messages(name,content,created_at) VALUES(?,?,?)',(name,content,now));mid=cur.lastrowid
        self.json_response(201,dict(id=mid,name=name,content=content,created_at=now))
    def do_OPTIONS(self):
        self.send_response(204);self.send_header('Allow','GET, HEAD, POST, OPTIONS');self.end_headers()
if __name__=='__main__':
    print('实验1 http://127.0.0.1:8761',flush=True)
    ThreadingHTTPServer(('127.0.0.1',8761),Handler).serve_forever()
