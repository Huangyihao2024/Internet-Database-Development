<?php
/** 配置独立数据库并初始化空库；黄奕浩 2413581，AI辅助。CLI专用。 */
if (PHP_SAPI !== 'cli') exit(1);
$root=dirname(__DIR__);$local=$root.'/.local';
$v=json_decode(file_get_contents($local.'/setup-input.json'),true,512,JSON_THROW_ON_ERROR);
$p=new PDO($v['dsn'].';charset=utf8mb4',$v['username'],$v['password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$tables=$p->query('SHOW TABLES')->fetchAll();
if (count($tables)===0) $p->exec(file_get_contents($root.'/data/install.sql'));
else if (count($tables)!==20) throw new RuntimeException('目标库不是空库或本项目20表结构，请选择专用新库。');
$config=['components'=>['db'=>['class'=>'yii\\db\\Connection','dsn'=>$v['dsn'],'username'=>$v['username'],'password'=>$v['password'],'charset'=>'utf8mb4'],'mailer'=>['class'=>'yii\\symfonymailer\\Mailer','viewPath'=>'@common/mail','useFileTransport'=>true]]];
file_put_contents($root.'/app/common/config/main-local.php',"<?php\n/** 本机数据库配置，请勿公开。 */\nreturn ".var_export($config,true).";\n");
echo "Database configured.\n";
