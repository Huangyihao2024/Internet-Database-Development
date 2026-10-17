<?php
/**
 * 知行学习团队：frontend/views/member/edit.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ $this->title='我的资料 · 知行'; ?><h1>编辑我的资料</h1><?php use yii\widgets\ActiveForm; ?><?php $f=ActiveForm::begin(['enableClientValidation'=>false]) ?><?= $f->errorSummary($model) ?><?= $f->field($model,'display_name') ?><?= $f->field($model,'student_number') ?><?= $f->field($model,'bio')->textarea() ?><button>保存资料</button><?php ActiveForm::end() ?>
