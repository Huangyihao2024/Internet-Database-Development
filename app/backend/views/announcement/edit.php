<?php
/**
 * 绿茵数据团队：backend/views/announcement/edit.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\widgets\ActiveForm;$this->title='编辑公告 · 绿茵视界'; ?><h1><?= $model->isNewRecord?'新增':'编辑' ?>公告</h1><?php $f=ActiveForm::begin(['enableClientValidation'=>false]) ?><?= $f->errorSummary($model) ?><?= $f->field($model,'title') ?><?= $f->field($model,'content')->textarea() ?><?= $f->field($model,'status')->dropDownList(['draft'=>'草稿','published'=>'已发布','archived'=>'已撤下']) ?><button>保存公告</button><?php ActiveForm::end() ?>
