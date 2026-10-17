<?php
/**
 * 绿茵数据团队：backend/views/team/update.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\widgets\ActiveForm;$this->title='团队介绍 · 绿茵视界'; ?><h1>团队介绍</h1><?php $f=ActiveForm::begin(['enableClientValidation'=>false]) ?><?= $f->errorSummary($model) ?><?= $f->field($model,'name') ?><?= $f->field($model,'description')->textarea() ?><button>保存团队介绍</button><?php ActiveForm::end() ?>
