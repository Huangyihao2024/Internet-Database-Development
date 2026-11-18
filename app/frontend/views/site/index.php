<?php
/**
 * 知行学习团队：frontend/views/site/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='知行学习团队 · 让学习留下成果'; ?>
<section class="hero"><div class="eyebrow">LEARN · BUILD · SHARE</div><h1><?= Html::encode($team->name??'知行学习团队') ?></h1><p class="prose"><?= Html::encode($team->description??'一起学习，协作实践，分享成果。') ?></p><?= Html::a('认识我们的成员',['member/index'],['class'=>'button']) ?></section>
<h2>分工协作，一起完成</h2><div class="grid"><?php foreach($members as $m): ?><article class="card"><span class="eyebrow">TEAM MEMBER</span><h2><?= Html::a(Html::encode($m->display_name),['member/view','id'=>$m->id]) ?></h2><p class="muted"><?= Html::encode($m->student_number) ?></p><p><?= Html::encode(mb_substr($m->bio??'',0,90)) ?></p></article><?php endforeach ?></div>
<h2>团队公告</h2><?php foreach($announcements as $m): ?><article class="card"><span class="muted"><?= Html::encode($m->published_at) ?></span><h3><?= Html::a(Html::encode($m->title),['announcement/view','id'=>$m->id]) ?></h3></article><?php endforeach ?><?php if(!$announcements): ?><p>暂无已发布公告。</p><?php endif ?>
