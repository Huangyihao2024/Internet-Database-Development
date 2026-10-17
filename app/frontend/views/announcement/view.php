<?php
/**
 * 绿茵数据团队：frontend/views/announcement/view.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title=$model->title.' · 绿茵视界'; ?><span class="muted"><?= Html::encode($model->published_at) ?></span><h1><?= Html::encode($model->title) ?></h1><article class="card prose"><?= Html::encode($model->content) ?></article><p><?= Html::a('返回公告列表',['index']) ?></p>
