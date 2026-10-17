<?php
/**
 * 知行学习团队：frontend/views/site/error.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='请求未完成'; ?><h1><?= Html::encode($name) ?></h1><p><?= Html::encode($message) ?></p><p><?= Html::a('返回首页',['site/index']) ?></p>
