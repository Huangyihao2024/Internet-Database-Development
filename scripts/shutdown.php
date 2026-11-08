<?php
/** 独立世界杯数据库正常关闭；本人负责，AI辅助编写。 */
if(PHP_SAPI!=='cli')exit(1);
$c=json_decode(file_get_contents($argv[1]),true,512,JSON_THROW_ON_ERROR);
$p=new PDO('mysql:host=127.0.0.1;port=13328','root',$c['root_password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$p->exec('SHUTDOWN');echo "World Cup database stopped.\n";
