<?php
/**
 * 知行学习团队：frontend/views/download/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='作业下载 · 知行'; ?><div class="eyebrow">COURSEWORK ARCHIVE</div><h1>课程作业下载</h1><p class="muted">当前提供已整理的文件。其他组员的个人作业将在提交后补充。</p><?php foreach(['team'=>'团队材料','personal'=>'个人作业'] as $scope=>$label): ?><h2><?= $label ?></h2><table><tr><th>文件</th><th>操作</th></tr><?php foreach($files as $f): if($f->scope!==$scope)continue; ?><tr><td><?= Html::encode($f->title) ?></td><td><?php if($f->resolvedPath()): ?><?= Html::a('下载',['file','id'=>$f->id],['class'=>'button']) ?><?php else: ?><span class="muted">文件暂不可用</span><?php endif ?></td></tr><?php endforeach ?></table><?php endforeach ?>
