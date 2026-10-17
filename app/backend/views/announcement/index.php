<?php
/**
 * 绿茵数据团队：backend/views/announcement/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='公告管理 · 绿茵视界'; ?><h1>公告管理</h1><p><?= Html::a('新增公告',['create'],['class'=>'button']) ?></p><table class="announcement-table"><tr><th>标题</th><th>状态</th><th>发布时间</th><th>操作</th></tr><?php foreach($provider->models as $m): ?><tr><td><?= Html::encode($m->title) ?></td><td><?= Html::encode(['draft'=>'草稿','published'=>'已发布','archived'=>'已撤下'][$m->status]) ?></td><td><?= Html::encode($m->published_at) ?></td><td><div class="actions"><?= Html::a('编辑',['update','id'=>$m->id]) ?><?= Html::beginForm(['delete','id'=>$m->id],'post') ?><button>删除</button><?= Html::endForm() ?></div></td></tr><?php endforeach ?></table><?= \yii\widgets\LinkPager::widget(['pagination'=>$provider->pagination]) ?>
