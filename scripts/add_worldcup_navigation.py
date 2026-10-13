"""在公共布局增加赛事导航，负责人王乐之（2411302），AI辅助开发。"""
from pathlib import Path
base=Path(__file__).resolve().parents[1]/'app'
changes={
 'frontend/views/layouts/main.php':("<?= Html::a(Yii::t('app','球迷社区'),['community/index']) ?>","<?= Html::a('比赛',['match/index']) ?><?= Html::a('球队',['team/index']) ?><?= Html::a('球员',['player/index']) ?><?= Html::a('积分榜',['standing/index']) ?><?= Html::a('图表',['statistics/index']) ?><?= Html::a('收藏',['favorite/index']) ?><?= Html::a(Yii::t('app','球迷社区'),['community/index']) ?>"),
 'backend/views/layouts/main.php':("<?= Html::a(Yii::t('app','团队介绍'),['team/update']) ?>","<?= Html::a('赛事维护',['data/index']) ?><?= Html::a('数据导入',['import/index']) ?><?= Html::a(Yii::t('app','团队介绍'),['team/update']) ?>"),
}
for name,(old,new) in changes.items():
    path=base/name;source=path.read_text(encoding='utf-8')
    if new not in source:
        assert old in source,name
        path.write_text(source.replace(old,new,1),encoding='utf-8')
