"""Stop only this project's recorded development processes and database."""
from pathlib import Path
import subprocess,os,json,shutil,socket
R=Path(__file__).resolve().parent.parent;L=R/'.local/oneclick';state=L/'state.json'
if not state.exists():raise SystemExit('No local deployment to stop.')
s=json.loads(state.read_text(encoding='utf-8'));marker=str(L/'release').replace("'","''")
for name in ['frontend','backend']:
 pid=s.get(name+'_pid')
 if pid:
  subprocess.run(['powershell','-NoProfile','-Command',f"$p=Get-CimInstance Win32_Process -Filter 'ProcessId = {int(pid)}'; if ($p -and $p.CommandLine.Contains('{marker}')) {{ Stop-Process -Id {int(pid)} }}"],check=True)
cmd=shutil.which('php');php=Path(os.environ.get('WORLD_CUP_PHP_ROOT') or (str(Path(cmd).parent) if cmd else str(R.parents[1]/'个人实验文件夹3/运行环境/php')))
try:
 with socket.create_connection(('127.0.0.1',13328),timeout=1):pass
except OSError:print('Database already stopped.')
else:subprocess.run([str(php/'php.exe'),'-c',str(L/'release/.local/php.ini'),str(R/'scripts/shutdown.php'),str(L/'credentials.json')],cwd=php,check=True)
print('World Cup development environment stopped. Data retained.')
