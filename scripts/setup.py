"""Configure a fresh checkout against a dedicated MySQL database. No existing database is dropped."""
from pathlib import Path
import argparse,os,subprocess,json,getpass,urllib.request,hashlib,secrets,sys
R=Path(__file__).resolve().parent.parent
p=argparse.ArgumentParser();p.add_argument('--php-root',required=True);p.add_argument('--dsn',required=True);p.add_argument('--db-user',required=True);p.add_argument('--password-stdin',action='store_true');args=p.parse_args()
php=Path(args.php_root).resolve();exe=php/'php.exe' if os.name=='nt' else php/'php';local=R/'.local';local.mkdir(exist_ok=True)
if (R/'app/common/config/main-local.php').exists():raise SystemExit('已有本机数据库配置，停止以免覆盖。请在全新克隆目录部署。')
pw=sys.stdin.readline().rstrip('\r\n') if args.password_stdin else getpass.getpass('MySQL database password: ')
if not pw:raise SystemExit('Database password must not be empty')
ini=local/'php.ini';ini.write_text('extension_dir="ext"\nextension=mbstring\nextension=curl\nextension=openssl\nextension=fileinfo\nextension=gd\nextension=pdo_mysql\n',encoding='utf-8')
composer=local/'composer.phar'
if not composer.exists():
 data=urllib.request.urlopen('https://getcomposer.org/composer-stable.phar',timeout=45).read()
 checksum=urllib.request.urlopen('https://getcomposer.org/download/latest-stable/composer.phar.sha256sum',timeout=45).read().decode().split()[0]
 if hashlib.sha256(data).hexdigest()!=checksum:raise SystemExit('Composer SHA256 mismatch')
 composer.write_bytes(data)
env={**os.environ,'COMPOSER_HOME':str(local/'composer-home'),'COMPOSER_CACHE_DIR':str(local/'composer-cache')}
# Honor the Windows system proxy when Composer does not inherit it automatically.
from urllib.request import getproxies
for kind,url in getproxies().items():
 if kind in ['http','https']:env.setdefault(kind.upper()+'_PROXY',url)
def run(*a):subprocess.run([str(exe),'-c',str(ini),*map(str,a)],cwd=php,env=env,check=True)
run(composer,'install','--working-dir='+str(R/'app'),'--prefer-dist','--no-interaction')
for part in ['frontend/runtime','backend/runtime','console/runtime','frontend/web/assets','backend/web/assets']:(R/'app'/part).mkdir(parents=True,exist_ok=True)
run(R/'app/init','--env=Development','--overwrite=All')
v=local/'setup-input.json';v.write_text(json.dumps({'dsn':args.dsn,'username':args.db_user,'password':pw}),encoding='utf-8')
try:run(R/'scripts/configure.php')
finally:v.unlink(missing_ok=True)
accounts=[]
for name,sid,user,role,bio in [('黄奕浩','2413581','huangyihao','admin','负责公共账户、社区审核回复、通知审计、部署文档与录屏。'),('王乐之','2411302','wanglezhi','member','负责世界杯赛事、球队球员、收藏统计、动态图表、设计和PPT。')]:accounts.append(dict(name=name,student_id=sid,username=user,role=role,bio=bio,password=secrets.token_urlsafe(16)))
f=local/'accounts.json';f.write_text(json.dumps(accounts,ensure_ascii=False,indent=2),encoding='utf-8');env['WORLD_CUP_ACCOUNT_FILE']=str(f);run(R/'app/yii','worldcup-setup/seed')
print('Setup complete. Local accounts: '+str(f))
print('Start frontend: php -c '+str(ini)+' -S 127.0.0.1:8781 -t '+str(R/'app/frontend/web'))
print('Start backend: php -c '+str(ini)+' -S 127.0.0.1:8782 -t '+str(R/'app/backend/web'))
print('Run those commands from the PHP directory so the relative extension directory resolves.')
