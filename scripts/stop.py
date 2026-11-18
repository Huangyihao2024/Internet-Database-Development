"""Stop only this project's recorded development processes and database."""
from pathlib import Path
import subprocess,os,json,shutil,socket,time
R=Path(__file__).resolve().parent.parent;L=R/'.local/oneclick';state=L/'state.json'
if not state.exists():raise SystemExit('No local deployment to stop.')
s=json.loads(state.read_text(encoding='utf-8'));marker=str(L/'release').replace("'","''")
subprocess.run(['powershell','-NoProfile','-Command',f"Get-CimInstance Win32_Process -Filter \"Name='php.exe'\" | Where-Object {{ $_.CommandLine -and $_.CommandLine.Contains('{marker}') -and $_.CommandLine.Contains('-S 127.0.0.1:878') }} | ForEach-Object {{ Stop-Process -Id $_.ProcessId }}"],check=True)
cmd=shutil.which('php');php=Path(os.environ.get('WORLD_CUP_PHP_ROOT') or (str(Path(cmd).parent) if cmd else str(R.parents[1]/'个人实验文件夹3/运行环境/php')))
try:
 with socket.create_connection(('127.0.0.1',13328),timeout=1):pass
except OSError:print('Database already stopped.')
else:subprocess.run([str(php/'php.exe'),'-c',str(L/'release/.local/php.ini'),str(R/'scripts/shutdown.php'),str(L/'credentials.json')],cwd=php,check=True)
for attempt in range(60):
 occupied=[]
 for port in [13328,8781,8782]:
  try:
   with socket.create_connection(('127.0.0.1',port),timeout=.2):occupied.append(port)
  except OSError:pass
 if not occupied:break
 time.sleep(.5)
else:raise RuntimeError('Service shutdown timed out: '+str(occupied))
print('World Cup development environment stopped. Data retained.')
