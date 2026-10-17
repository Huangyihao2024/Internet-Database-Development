<?php
/**
 * 绿茵数据团队：backend/views/member/index.php
 * 负责人：黄奕浩 2413581；由AI辅助编写，保留原Yii框架署名。
 */ use yii\helpers\Html;$this->title='成员管理 · 绿茵视界'; ?><h1>成员资料</h1><table><tr><th>姓名</th><th>学号</th><th>个人介绍</th><th>操作</th></tr><?php foreach($models as $m): ?><tr><td><?= Html::encode($m->display_name) ?></td><td><?= Html::encode($m->student_number) ?></td><td><?= Html::encode($m->bio) ?></td><td><?= Html::a('编辑',['update','id'=>$m->id]) ?></td></tr><?php endforeach ?></table>
