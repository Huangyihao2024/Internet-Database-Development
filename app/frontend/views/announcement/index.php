<?php
/**
 * 知行学习团队：frontend/views/announcement/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='团队公告 · 知行'; ?><h1>团队公告</h1><?php foreach($models as $m): ?><article class="card"><span class="muted"><?= Html::encode($m->published_at) ?></span><h2><?= Html::a(Html::encode($m->title),['view','id'=>$m->id]) ?></h2></article><?php endforeach ?><?= \yii\widgets\LinkPager::widget(['pagination'=>$pages]) ?>
