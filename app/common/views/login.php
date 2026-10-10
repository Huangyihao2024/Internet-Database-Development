<?php
/**
 * 绿茵数据团队：common/views/login.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;use yii\widgets\ActiveForm;$this->title='登录 · 绿茵视界'; ?>
<div class="narrow"><div class="eyebrow">WELCOME BACK</div><h1>登录<?= Yii::$app->id==='app-backend'?'管理后台':'绿茵数据团队' ?></h1><p class="muted">使用你的账号继续访问。</p><?php $f=ActiveForm::begin(['enableClientValidation'=>false]) ?><?= $f->errorSummary($model) ?><?= $f->field($model,'username')->label('用户名')->textInput(['autocomplete'=>'username']) ?><?= $f->field($model,'password')->label('密码')->passwordInput(['autocomplete'=>'current-password']) ?><button>登录</button><?php ActiveForm::end() ?></div>
