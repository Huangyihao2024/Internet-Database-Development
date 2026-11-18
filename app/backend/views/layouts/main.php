<?php
/**
 * 知行学习团队：backend/views/layouts/main.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */
use yii\helpers\{Html,Url};
$backend=Yii::$app->id==='app-backend';
$this->registerCssFile('@web/css/zhixing.css');
?>
<?php $this->beginPage() ?><!doctype html><html lang="<?= Html::encode(Yii::$app->language) ?>"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><?= Html::csrfMetaTags() ?><title><?= Html::encode($this->title?:'知行学习团队') ?></title><?php $this->head() ?></head><body><?php $this->beginBody() ?>
<header><strong><?= Html::a('知行 / '.($backend?'内容管理':'学习团队'),['site/index']) ?></strong><nav>
<?php if($backend): ?>
<?= Html::a(Yii::t('app','团队介绍'),['team/update']) ?><?= Html::a(Yii::t('app','成员管理'),['member/index']) ?><?= Html::a(Yii::t('app','公告管理'),['announcement/index']) ?><?= Html::a(Yii::t('app','查看前台'),(getenv('ZHIXING_FRONT_URL') ?: 'http://127.0.0.1:8771/')) ?>
<?php else: ?><?= Html::a(Yii::t('app','成员'),['member/index']) ?><?= Html::a(Yii::t('app','公告'),['announcement/index']) ?><?= Html::a(Yii::t('app','作业下载'),['download/index']) ?><?= Html::a(Yii::t('app','管理后台'),(getenv('ZHIXING_BACK_URL') ?: 'http://127.0.0.1:8772/')) ?><?php endif ?>
<?php if(Yii::$app->user->isGuest): ?><?= Html::a(Yii::t('app','登录'),['site/login']) ?><?php if(!$backend): ?><?= Html::a(Yii::t('app','注册'),['site/signup']) ?><?php endif ?>
<?php else: ?><?php if(!$backend): ?><?= Html::a(Yii::t('app','我的资料'),['member/edit']) ?><?php endif ?><?= Html::beginForm(['site/logout'],'post') ?><button class="link">退出（<?= Html::encode(Yii::$app->user->identity->username) ?>）</button><?= Html::endForm() ?><?php endif ?>
<?= Html::a('中文',\yii\helpers\Url::current(['lang'=>'zh-CN'])) ?><?= Html::a('English',\yii\helpers\Url::current(['lang'=>'en-US'])) ?><?= Html::a('留言审核',['moderation/index']) ?><?= Html::a('操作日志',['audit/index']) ?><?= Html::a('邮件记录',['notification/index']) ?></nav></header><main><?php foreach(Yii::$app->session->getAllFlashes() as $flash): ?><div class="alert" role="status"><?= Html::encode($flash) ?></div><?php endforeach ?><?= $content ?></main>
<footer>知行学习团队 · 互联网数据库开发课程设计<br>黄奕浩 2413581 / 王乐之 2411302</footer><?php $this->endBody() ?></body></html><?php $this->endPage() ?>
