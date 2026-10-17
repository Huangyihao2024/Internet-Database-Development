<?php
/**
 * 知行学习团队：frontend/views/member/view.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title=$model->display_name.' · 知行'; ?><div class="eyebrow">MEMBER PROFILE</div><h1><?= Html::encode($model->display_name) ?></h1><p class="muted"><?= Html::encode($model->student_number) ?></p><article class="card prose"><?= Html::encode($model->bio) ?></article><p><?= Html::a('返回成员列表',['index']) ?></p>
