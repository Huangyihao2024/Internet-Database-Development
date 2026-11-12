# 互联网数据库开发

知行学习团队课程项目：Yii2 Advanced + MySQL。组长黄奕浩（2413581），组员王乐之（2411302）、黄乐仁（2413628）。

## 当前完成

黄奕浩的公共模块已实现：注册登录、管理员与成员权限、团队和成员展示、本人资料编辑、后台资料维护、公告管理与发布、课程作业下载。资源与活动交流由其他成员继续开发。

## 目录

- app：Yii2前后台、公共Model与控制器、样例初始化命令；composer.lock锁定依赖。
- data/install.sql：15张业务表的MySQL结构。
- data/team：团队需求、部署、实现文档的TeX/PDF及截图。
- data/personal：黄奕浩三份个人报告PDF。
- coursework：三个个人实验的自编源码、报告TeX与引用图片，不含下载的运行时和模板包。
- docs：课件要求、个人分工、协作规范、接口交接与展示提纲。
- scripts：从新克隆配置数据库的初始化工具。

## 新克隆部署

准备PHP 8.3+（pdo_mysql、mbstring、curl、openssl）、Python、Git、MySQL 8。创建专用空数据库及专用数据库用户，然后运行：

```powershell
python scripts/setup.py --php-root "D:/your-php" --dsn "mysql:host=127.0.0.1;port=3306;dbname=zhixing_team" --db-user zhixing_app
```

密码由交互输入；本机数据库配置与随机登录账号仅存放在忽略目录。脚本不会删除原有数据库。已有配置时停止，请在全新克隆目录运行。使用本机实际代理，不关闭TLS校验。

在PHP目录，使用生成的.local/php.ini启动两个开发服务，文档根目录分别为app/frontend/web、app/backend/web，端口8771、8772。参见data/team的部署文档。PHP内置服务器只用于本机开发。

## 课程规范

统一命名；提交的PHP文件有注释头；每人负责MVC各至少一个文件；团队/个人作业按data/team、data/personal组织，下载中心提供真实文件链接。

## 公开内容范围

不提交口令、本机配置、数据库数据目录、依赖vendor、缓存、测试脚本和测试产物。个人完整提交ZIP及第三方运行时/模板保留本机，Git中提供自编源码与报告；完整课程提交包另行整理。第三方Yii文件保留原许可证，AI辅助编写的团队代码在注释中标明。

当前尚未完成另外两人的业务模块、最终展示PPT与录屏；Tower团队和协作者邀请仍需本人登录操作。

## 一键部署与完整要求

双击根目录“一键部署.cmd”即可自动完成独立建库、配置、初始化、启动和检查（预先安装PHP 8.3、MySQL 8、Python）。详见docs/08_一键部署使用.md。全部课件必做、选做、建议与加分项逐项分配见docs/07_全量要求与加分任务.md。截图统一位于根目录figures；实现文档已按课件名称提供，Tower真实操作仍需本人登录完成。
