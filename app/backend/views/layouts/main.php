<?php
/**
 * 知行学习团队：backend/views/layouts/main.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
use yii\helpers\{Html,Url};
$backend=Yii::$app->id==='app-backend';
$this->registerCssFile('@web/css/zhixing.css');
?>
<?php $this->beginPage() ?><!doctype html><html lang="zh-CN"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?= Html::csrfMetaTags() ?><title><?= Html::encode($this->title?:'知行学习团队') ?></title><?php $this->head() ?></head><body><?php $this->beginBody() ?>
<header><strong><?= Html::a('知行 / '.($backend?'内容管理':'学习团队'),['site/index']) ?></strong><nav>
<?php if($backend): ?>
<?= Html::a('团队介绍',['team/update']) ?><?= Html::a('成员管理',['member/index']) ?><?= Html::a('公告管理',['announcement/index']) ?><?= Html::a('查看前台','http://127.0.0.1:8771/') ?>
<?php else: ?><?= Html::a('成员',['member/index']) ?><?= Html::a('公告',['announcement/index']) ?><?= Html::a('作业下载',['download/index']) ?><?= Html::a('管理后台','http://127.0.0.1:8772/') ?><?php endif ?>
<?php if(Yii::$app->user->isGuest): ?><?= Html::a('登录',['site/login']) ?><?php if(!$backend): ?><?= Html::a('注册',['site/signup']) ?><?php endif ?>
<?php else: ?><?php if(!$backend): ?><?= Html::a('我的资料',['member/edit']) ?><?php endif ?><?= Html::beginForm(['site/logout'],'post') ?><button class="link">退出（<?= Html::encode(Yii::$app->user->identity->username) ?>）</button><?= Html::endForm() ?><?php endif ?>
</nav></header><main><?php foreach(Yii::$app->session->getAllFlashes() as $flash): ?><div class="alert" role="status"><?= Html::encode($flash) ?></div><?php endforeach ?><?= $content ?></main>
<footer>知行学习团队 · 互联网数据库开发课程设计<br>黄奕浩 2413581 / 王乐之 2411302 / 黄乐仁 2413628</footer><?php $this->endBody() ?></body></html><?php $this->endPage() ?>
