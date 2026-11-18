<?php
/** 团队留言、审核与公共服务；本人负责，AI辅助实现。 */ use yii\helpers\Html;$this->title='邮件投递记录'; ?><h1>邮件投递记录</h1><p>当前采用本机文件投递，不发送外部邮件。审核通知可在运行环境的后台runtime/mail目录查看原始EML。</p><table><tr><th>邮件文件</th><th>生成时间</th></tr><?php foreach(array_slice($files,0,50) as $f): ?><tr><td><?= Html::encode(basename($f)) ?></td><td><?= date('Y-m-d H:i:s',filemtime($f)) ?></td></tr><?php endforeach ?></table>
