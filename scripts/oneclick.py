"""Windows one-click deployment: independent database, configuration, seed and two servers."""
from pathlib import Path
import os,sys,shutil,subprocess,json,secrets,socket,time,hashlib,urllib.request
R=Path(__file__).resolve().parent.parent;L=R/'.local/oneclick';L.mkdir(parents=True,exist_ok=True)
subprocess.run([sys.executable,str(R/'scripts/check_worldcup_data.py')],check=True)
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
P=Path(os.environ.get('WORLD_CUP_PHP_ROOT') or (str(Path(phpcmd).parent) if phpcmd else str(R.parents[1]/'个人实验文件夹3/运行环境/php')))
M=Path(os.environ.get('WORLD_CUP_MYSQL_ROOT') or (str(Path(mysqlcmd).parent.parent) if mysqlcmd else 'D:/MySQL/MySQL Server 8.0'))
if not (P/'php.exe').exists() or not (M/'bin/mysqld.exe').exists():raise SystemExit('Install PHP 8.3+ and MySQL 8, or set WORLD_CUP_PHP_ROOT / WORLD_CUP_MYSQL_ROOT. No database setup is required manually.')
# MySQL 在部分 Windows 环境下无法从中文安装路径加载插件，给程序目录建立 ASCII 别名。
if any(ord(c)>127 for c in str(M)):
 mysql_alias=Path('D:/CodexTools')/('mysql-'+hashlib.sha256(str(M).encode()).hexdigest()[:10])
 mysql_alias.parent.mkdir(parents=True,exist_ok=True)
 if not mysql_alias.exists():run(['cmd','/c','mklink','/J',str(mysql_alias),str(M)],stdout=subprocess.DEVNULL)
 M=mysql_alias
state=L/'state.json';release=L/'release';alias=Path('D:/CodexTools')/('worldcup-deploy-'+hashlib.sha256(str(R).encode()).hexdigest()[:10])
alias.parent.mkdir(parents=True,exist_ok=True)
if not alias.exists():run(['cmd','/c','mklink','/J',str(alias),str(L)],stdout=subprocess.DEVNULL)
def spawn(name,args,cwd):
 log=(L/(name+'.log')).open('ab');p=subprocess.Popen(list(map(str,args)),cwd=cwd,env={**os.environ,'WORLD_CUP_FRONT_URL':'http://127.0.0.1:8781/','WORLD_CUP_BACK_URL':'http://127.0.0.1:8782/'},stdout=log,stderr=log,creationflags=subprocess.CREATE_NO_WINDOW);return p.pid
def ignore(src,names):
 skip={n for n in names if n in ['.git','.local','vendor','node_modules','runtime','tests','_build','__pycache__'] or n.endswith(('.log','.zip','.aux','.out','.toc'))}
 if Path(src).name=='config' and 'environments' not in Path(src).parts:skip|={n for n in names if n.endswith('-local.php')}
 if Path(src).resolve()==(R/'data/personal').resolve():skip-={n for n in names if n.endswith('.zip')}
 return skip
if state.exists():
 s=json.loads(state.read_text(encoding='utf-8'));print('Reusing this deployment; no database is overwritten.');shutil.copytree(R,release,ignore=ignore,dirs_exist_ok=True)
else:
 if any(port(p) for p in [13328,8781,8782]):raise SystemExit('Ports 13328/8781/8782 are occupied. Existing services will not be touched.')
 shutil.copytree(R,release,ignore=ignore,dirs_exist_ok=True)
 # Reuse installed immutable dependencies when present; a fresh checkout installs from composer.lock.
 if (R/'app/vendor').exists() and not (release/'app/vendor').exists():shutil.copytree(R/'app/vendor',release/'app/vendor')
 (release/'.local').mkdir(exist_ok=True)
 for candidate in [R/'.local/composer.phar',R.parents[0]/'软件下载/composer.phar']:
  if candidate.exists():shutil.copy2(candidate,release/'.local/composer.phar');break
 ini=L/'php.ini';ini.write_text('extension_dir="ext"\nextension=pdo_mysql\nextension=mbstring\nextension=openssl\nextension=curl\nextension=zip\n',encoding='utf-8')
 data=alias/'mysql-data'
 if data.exists():raise SystemExit('Unfinished deployment data exists. Inspect .local/oneclick logs; do not overwrite it.')
 run([M/'bin/mysqld.exe','--no-defaults','--initialize-insecure','--basedir='+str(M),'--datadir='+str(data)],stdout=subprocess.DEVNULL,stderr=subprocess.DEVNULL)
 s={'root_password':secrets.token_hex(24),'app_password':secrets.token_hex(24)}
 (L/'credentials.json').write_text(json.dumps(s),encoding='utf-8')
 dbargs=[M/'bin/mysqld.exe','--no-defaults','--basedir='+str(M),'--datadir='+str(data),'--log-error='+str(alias/'mysql.log'),'--bind-address=127.0.0.1','--port=13328','--mysqlx=OFF']
 s['mysql_pid']=spawn('mysql',dbargs,alias);wait(13328)
 provision=L/'provision.php';provision.write_text('''<?php
/** One-click isolated database provisioning; team maintainer, AI assisted. */
$c=json_decode(file_get_contents(__DIR__.'/credentials.json'),true);
$p=new PDO('mysql:host=127.0.0.1;port=13328','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$p->exec("ALTER USER 'root'@'localhost' IDENTIFIED BY '".$c['root_password']."'");
$p->exec("CREATE DATABASE wc_portal CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$p->exec("CREATE USER 'wc_app'@'localhost' IDENTIFIED BY '".$c['app_password']."'");
$p->exec("GRANT ALL ON wc_portal.* TO 'wc_app'@'localhost'");
''',encoding='utf-8')
 run([P/'php.exe','-c',ini,provision],cwd=P);provision.unlink()
 run([sys.executable,release/'scripts/setup.py','--php-root',P,'--dsn','mysql:host=127.0.0.1;port=13328;dbname=wc_portal','--db-user','wc_app','--password-stdin'],input=(s['app_password']+'\n').encode())
 state.write_text(json.dumps(s),encoding='utf-8')
if not port(13328):s['mysql_pid']=spawn('mysql',[M/'bin/mysqld.exe','--no-defaults','--basedir='+str(M),'--datadir='+str(alias/'mysql-data'),'--log-error='+str(alias/'mysql.log'),'--bind-address=127.0.0.1','--port=13328','--mysqlx=OFF'],alias);wait(13328)
ini=release/'.local/php.ini'
if 'extension=gd' not in ini.read_text(encoding='utf-8'):ini.write_text(ini.read_text(encoding='utf-8')+'\nextension=gd\n',encoding='utf-8')
marker=str(release).replace("'","''")
run(['powershell','-NoProfile','-Command',f"Get-CimInstance Win32_Process -Filter \"Name='php.exe'\" | Where-Object {{ $_.CommandLine -and $_.CommandLine.Contains('{marker}') -and $_.CommandLine.Contains('-S 127.0.0.1:878') }} | ForEach-Object {{ Stop-Process -Id $_.ProcessId }}"])
for side,p in [('frontend',8781),('backend',8782)]:
 if not port(p):s[side+'_pid']=spawn(side,[P/'php.exe','-c',release/'.local/php.ini','-S','127.0.0.1:'+str(p),'-t',release/'app'/side/'web'],P);wait(p)
run([P/'php.exe','-c',release/'.local/php.ini',release/'app/yii','worldcup-setup/upgrade'],cwd=P)
run([P/'php.exe','-c',release/'.local/php.ini',release/'app/yii','worldcup-setup/ensure-tournament'],cwd=P)
run([P/'php.exe','-c',release/'.local/php.ini',release/'app/yii','worldcup-setup/sync-downloads'],cwd=P)
opener=urllib.request.build_opener(urllib.request.ProxyHandler({}))
for p in [8781,8782]:
 with opener.open('http://127.0.0.1:'+str(p),timeout=20) as response:
  text=response.read().decode();assert response.status==200 and '<title>' in text and 'Fatal error' not in text
for route in ['match/index','team/index','player/index','standing/index','statistics/index','community/index','download/index']:
 with opener.open('http://127.0.0.1:8781/index.php?r='+route,timeout=20) as response:
  text=response.read().decode();assert response.status==200 and '<title>' in text and 'Fatal error' not in text,route
state.write_text(json.dumps(s),encoding='utf-8');print('DEPLOYMENT PASSED: http://127.0.0.1:8781/ | http://127.0.0.1:8782/');print('Local accounts: '+str(release/'.local/accounts.json'))
