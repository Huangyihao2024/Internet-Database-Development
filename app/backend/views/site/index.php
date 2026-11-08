<?php
/**
 * 绿茵数据团队：backend/views/site/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='内容管理 · 绿茵视界'; ?><div class="eyebrow">CONTENT WORKSPACE</div><h1>团队内容管理</h1><div class="grid"><?php foreach($counts as $label=>$count): ?><div class="card"><span class="muted"><?= Html::encode($label) ?></span><h1><?= (int)$count ?></h1></div><?php endforeach ?></div><h2>开始维护</h2><div class="actions"><?= Html::a('编辑团队介绍',['team/update'],['class'=>'button']) ?><?= Html::a('管理成员',['member/index'],['class'=>'button']) ?><?= Html::a('发布公告',['announcement/create'],['class'=>'button']) ?></div>
