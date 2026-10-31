<?php
/** 绿茵视界公共及社区模块；本人负责，AI辅助编写。 */ use yii\helpers\Html;$this->title='通知预览'; ?><span class="eyebrow">NOTIFICATION PREVIEW</span><h1><?= Html::encode($model->subject) ?></h1><p class="meta">渠道 <?= Html::encode($model->channel) ?> · 状态 <?= Html::encode($model->status) ?></p><article class="card prose"><?= Html::encode($model->content) ?></article>
