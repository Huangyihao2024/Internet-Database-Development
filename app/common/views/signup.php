<?php
/**
 * 知行学习团队：common/views/signup.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\widgets\ActiveForm;$this->title='注册 · 知行'; ?><div class="narrow"><h1>加入学习交流</h1><p>注册账户默认拥有普通成员权限。</p><?php $f=ActiveForm::begin(['enableClientValidation'=>false]) ?><?= $f->errorSummary($model) ?><?= $f->field($model,'username') ?><?= $f->field($model,'email')->input('email') ?><?= $f->field($model,'password')->passwordInput() ?><?= $f->field($model,'verifyCode')->widget(\yii\captcha\Captcha::class,['captchaAction'=>'site/captcha','options'=>['autocomplete'=>'off']]) ?><button>创建账户</button><?php ActiveForm::end() ?></div>
