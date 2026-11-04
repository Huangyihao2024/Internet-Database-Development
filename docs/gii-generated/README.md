# Gii 实际生成记录

执行日期：2026-11-25。环境：PHP 8.3.37、MySQL 8.0.46、Yii2 Advanced、`yiisoft/yii2-gii` 2.2.7。

使用已配置的 `app/yii` 运行 Gii CLI。11 张本人负责表分别执行以下形式的命令，`--tableName` 与 `--modelClass` 按表替换：

```text
php yii gii/model --tableName=wc_data_match --modelClass=FootballMatch --ns=common\models\gii --generateRelations=all --interactive=0 --overwrite=0
php yii gii/crud --modelClass=common\models\FootballMatch --controllerClass=backend\controllers\GiiMatchController --searchModelClass=backend\models\GiiMatchSearch --interactive=0 --overwrite=0
```

Model 生成表：`wc_data_tournament`、`wc_data_stage`、`wc_data_group`、`wc_data_stadium`、`wc_data_team`、`wc_data_player`、`wc_data_squad`、`wc_data_match`、`wc_data_event`、`wc_fan_team_favorite`、`wc_fan_match_favorite`。Gii 命令均显示 `Files were generated successfully!`，保存了11个 Model 原始输出；CRUD 命令生成控制器、搜索模型和6个视图。

这些文件是**未加业务权限的 Gii 基线**，归档在本目录，不作为运行控制器加载。运行中的 `app/common/models/`、`app/backend/controllers/DataController.php` 和相应业务视图保留了字段验证、关联、管理员访问、POST/CSRF、事务和错误反馈等手工适配。可直接对比本目录与运行文件。后台 Gii 页面截图为 `figures/33_gii.png`。

最初的业务模型先由仓库脚本构建；本记录是随后在真实数据库上补做的 Gii 生成与对照，未将早期代码冒称为 Gii 原始输出。
