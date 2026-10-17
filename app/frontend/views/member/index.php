<?php
/**
 * 知行学习团队：frontend/views/member/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='团队成员 · 知行'; ?><div class="eyebrow">OUR TEAM</div><h1>认识知行</h1><p class="muted">从公共功能、学习资源到活动交流，每位成员负责一个完整模块。</p><div class="grid"><?php foreach($members as $m): ?><article class="card"><h2><?= Html::encode($m->display_name) ?></h2><p><?= Html::encode($m->student_number) ?></p><p class="prose"><?= Html::encode($m->bio) ?></p><?= Html::a('查看介绍',['view','id'=>$m->id],['class'=>'button']) ?></article><?php endforeach ?></div>
