"""Windows one-click deployment: independent database, configuration, seed and two servers."""
from pathlib import Path
import os,sys,shutil,subprocess,json,secrets,socket,time,hashlib,urllib.request
R=Path(__file__).resolve().parent.parent;L=R/'.local/oneclick';L.mkdir(parents=True,exist_ok=True)
def run(cmd,**kw):subprocess.run(list(map(str,cmd)),check=True,**kw)
def port(p):
 try:
  with socket.create_connection(('127.0.0.1',p),timeout=.5):return True
 except OSError:return False
def wait(p):
 for _ in range(120):
  if port(p):return
  time.sleep(.5)
 raise RuntimeError('Service timeout: '+str(p))
if os.name!='nt':raise SystemExit('This batch deployment targets Windows.')
phpcmd=shutil.which('php');mysqlcmd=shutil.which('mysqld') or shutil.which('mysql')
P=Path(os.environ.get('ZHIXING_PHP_ROOT') or (str(Path(phpcmd).parent) if phpcmd else str(R.parents[1]/'个人实验文件夹3/运行环境/php')))
M=Path(os.environ.get('ZHIXING_MYSQL_ROOT') or (str(Path(mysqlcmd).parent.parent) if mysqlcmd else 'D:/MySQL/MySQL Server 8.0'))
if not (P/'php.exe').exists() or not (M/'bin/mysqld.exe').exists():raise SystemExit('Install PHP 8.3+ and MySQL 8, or set ZHIXING_PHP_ROOT / ZHIXING_MYSQL_ROOT. No database setup is required manually.')
state=L/'state.json';release=L/'release';alias=Path('D:/CodexTools')/('zhixing-deploy-'+hashlib.sha256(str(R).encode()).hexdigest()[:10])
alias.parent.mkdir(parents=True,exist_ok=True)
if not alias.exists():run(['cmd','/c','mklink','/J',str(alias),str(L)],stdout=subprocess.DEVNULL)
def spawn(name,args,cwd):
 log=(L/(name+'.log')).open('ab');p=subprocess.Popen(list(map(str,args)),cwd=cwd,env={**os.environ,'ZHIXING_FRONT_URL':'http://127.0.0.1:8775/','ZHIXING_BACK_URL':'http://127.0.0.1:8776/'},stdout=log,stderr=log,creationflags=subprocess.CREATE_NO_WINDOW);return p.pid
def ignore(src,names):
 skip={n for n in names if n in ['.git','.local','vendor','node_modules','runtime','tests','_build','__pycache__'] or n.endswith(('.log','.zip','.aux','.out','.toc'))}
 if Path(src).name=='config' and 'environments' not in Path(src).parts:skip|={n for n in names if n.endswith('-local.php')}
 return skip
if state.exists():
 s=json.loads(state.read_text(encoding='utf-8'));print('Reusing this deployment; no database is overwritten.');shutil.copytree(R,release,ignore=ignore,dirs_exist_ok=True)
else:
 if any(port(p) for p in [13318,8775,8776]):raise SystemExit('Ports 13318/8775/8776 are occupied. Existing services will not be touched.')
 shutil.copytree(R,release,ignore=ignore,dirs_exist_ok=True)
 # Reuse installed immutable dependencies when present; a fresh checkout installs from composer.lock.
 if (R/'app/vendor').exists() and not (release/'app/vendor').exists():run(['cmd','/c','mklink','/J',str(release/'app/vendor'),str(R/'app/vendor')],stdout=subprocess.DEVNULL)
 (release/'.local').mkdir(exist_ok=True)
 for candidate in [R/'.local/composer.phar',R.parents[0]/'软件下载/composer.phar']:
  if candidate.exists():shutil.copy2(candidate,release/'.local/composer.phar');break
 ini=L/'php.ini';ini.write_text('extension_dir="ext"\nextension=pdo_mysql\nextension=mbstring\nextension=openssl\nextension=curl\n',encoding='utf-8')
 data=alias/'mysql-data'
 if data.exists():raise SystemExit('Unfinished deployment data exists. Inspect .local/oneclick logs; do not overwrite it.')
 run([M/'bin/mysqld.exe','--no-defaults','--initialize-insecure','--basedir='+str(M),'--datadir='+str(data)],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
 s={'root_password':secrets.token_hex(24),'app_password':secrets.token_hex(24)}
 (L/'credentials.json').write_text(json.dumps(s),encoding='utf-8')
 dbargs=[M/'bin/mysqld.exe','--no-defaults','--basedir='+str(M),'--datadir='+str(data),'--log-error='+str(alias/'mysql.log'),'--bind-address=127.0.0.1','--port=13318','--mysqlx=OFF']
 s['mysql_pid']=spawn('mysql',dbargs,alias);wait(13318)
 provision=L/'provision.php';provision.write_text('''<?php
/** One-click isolated database provisioning; team maintainer, AI assisted. */
$c=json_decode(file_get_contents(__DIR__.'/credentials.json'),true);
$p=new PDO('mysql:host=127.0.0.1;port=13318','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$p->exec("ALTER USER 'root'@'localhost' IDENTIFIED BY '".$c['root_password']."'");
$p->exec("CREATE DATABASE zhixing_team CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$p->exec("CREATE USER 'zhixing_app'@'localhost' IDENTIFIED BY '".$c['app_password']."'");
$p->exec("GRANT ALL ON zhixing_team.* TO 'zhixing_app'@'localhost'");
''',encoding='utf-8')
 run([P/'php.exe','-c',ini,provision],cwd=P);provision.unlink()
 run([sys.executable,release/'scripts/setup.py','--php-root',P,'--dsn','mysql:host=127.0.0.1;port=13318;dbname=zhixing_team','--db-user','zhixing_app','--password-stdin'],input=(s['app_password']+'\n').encode())
 state.write_text(json.dumps(s),encoding='utf-8')
if not port(13318):s['mysql_pid']=spawn('mysql',[M/'bin/mysqld.exe','--no-defaults','--basedir='+str(M),'--datadir='+str(alias/'mysql-data'),'--log-error='+str(alias/'mysql.log'),'--bind-address=127.0.0.1','--port=13318','--mysqlx=OFF'],alias);wait(13318)
for obsolete in (release/'data/team').glob('*2413581_2411302_2413628*'):
 if obsolete.is_file():obsolete.unlink()
for obsolete in [release/'docs/个人分工/2413628_黄乐仁_分工.md',release/'docs/02_三人分工与协作.md']:
 if obsolete.exists():obsolete.unlink()
accounts_file=release/'.local/accounts.json'
if accounts_file.exists():accounts_file.write_text(json.dumps([x for x in json.loads(accounts_file.read_text(encoding='utf-8')) if x['student_id'] in ['2413581','2411302']],ensure_ascii=False,indent=2),encoding='utf-8')
ini=release/'.local/php.ini'
if 'extension=gd' not in ini.read_text(encoding='utf-8'):ini.write_text(ini.read_text(encoding='utf-8')+'\nextension=gd\n',encoding='utf-8')
for name in ['frontend','backend']:
 pid=s.get(name+'_pid')
 if pid:
  marker=str(release).replace("'","''")
  run(['powershell','-NoProfile','-Command',f"$p=Get-CimInstance Win32_Process -Filter 'ProcessId = {int(pid)}'; if ($p -and $p.CommandLine.Contains('{marker}')) {{Stop-Process -Id {int(pid)}}}"])
for side,p in [('frontend',8775),('backend',8776)]:
 if not port(p):s[side+'_pid']=spawn(side,[P/'php.exe','-c',release/'.local/php.ini','-S','127.0.0.1:'+str(p),'-t',release/'app'/side/'web'],P);wait(p)
run([P/'php.exe','-c',release/'.local/php.ini',release/'app/yii','team-setup/upgrade'],cwd=P)
run([P/'php.exe','-c',release/'.local/php.ini',release/'app/yii','team-setup/sync-downloads'],cwd=P)
opener=urllib.request.build_opener(urllib.request.ProxyHandler({}))
for p in [8775,8776]:
 with opener.open('http://127.0.0.1:'+str(p),timeout=20) as response:
  text=response.read().decode();assert response.status==200 and '<title>' in text and 'Fatal error' not in text
state.write_text(json.dumps(s),encoding='utf-8');print('DEPLOYMENT PASSED: http://127.0.0.1:8775/ | http://127.0.0.1:8776/');print('Local accounts: '+str(release/'.local/accounts.json'))
