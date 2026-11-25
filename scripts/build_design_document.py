# -*- coding: utf-8 -*-
"""从最终 SQL 生成20表数据字典与可编辑 Mermaid 图；负责人王乐之（2411302），AI辅助开发。"""
from pathlib import Path
import re
root=Path(__file__).resolve().parents[1]
sql=(root/'data/install.sql').read_text(encoding='utf-8')
tables=[]
for match in re.finditer(r'CREATE TABLE `([^`]+)` \((.*?)\n\) ENGINE=',sql,re.S):
    name,body=match.groups()
    columns=[]
    for line in body.splitlines():
        m=re.match(r'\s*`([^`]+)`\s+(.+?)(?:,)?$',line)
        if m:
            field,decl=m.groups();comment=re.search(r"COMMENT '([^']*)'",decl)
            columns.append((field,decl.split(' COLLATE')[0].split(' COMMENT')[0].rstrip(','),comment[1] if comment else ''))
    indexes=re.findall(r'(?:UNIQUE KEY|KEY) `([^`]+)` \(([^)]+)\)',body)
    foreign=re.findall(r'FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)',body)
    tables.append((name,columns,indexes,foreign))
assert len(tables)==20,len(tables)
fig=root/'figures';fig.mkdir(exist_ok=True)
er=['erDiagram']
for name,columns,indexes,foreign in tables:
    er.append('    '+name+' {')
    for field,decl,_ in columns:
        typ=re.match(r'\w+',decl)[0]
        er.append(f'        {typ} {field}'+(' PK' if field=='id' else ''))
    er.append('    }')
    for field,target,targetfield in foreign:er.append(f'    {target} ||--o{{ {name} : "{field}"')
(fig/'绿茵数据团队_20表ER图(2413581_2411302).mmd').write_text('\n'.join(er)+'\n',encoding='utf-8')
modules='''flowchart LR
  V[访客] --> Q[赛事查询]
  V --> C[球迷社区]
  U[登录成员] --> F[球队与比赛收藏]
  U --> C
  A[管理员] --> M[赛事后台维护]
  A --> I[数据导入]
  A --> R[审核公告与日志]
  Q --> S[积分与射手统计]
  Q --> D[四类动态图表]
  I --> DB[(MySQL 20表)]
  M --> DB
  F --> DB
  C --> DB
  S --> DB
  D --> DB
'''
flow='''flowchart TD
  A[原始文本与公开球员名单] --> B[来源摘要和许可记录]
  B --> C[解析64场及事件]
  C --> D{数量、引用、比分校验}
  D -- 失败 --> E[预览错误并停止]
  D -- 通过 --> F[管理员确认POST]
  F --> G[MySQL事务]
  G --> H[按自然键更新届次、球队、球员、比赛、事件]
  H --> I{外键或唯一键错误}
  I -- 是 --> J[回滚并反馈]
  I -- 否 --> K[提交]
  K --> L[查询、积分与动态可视化]
'''
(fig/'绿茵数据团队_系统模块图(2413581_2411302).mmd').write_text(modules,encoding='utf-8')
(fig/'绿茵数据团队_数据导入流程图(2413581_2411302).mmd').write_text(flow,encoding='utf-8')
doc=['# 绿茵数据团队_数据库与赛事模块设计(2413581_2411302)','','负责人：王乐之（2411302）；AI辅助整理。结构依据 `data/install.sql`，图源文件见 `figures/*.mmd`。','','## 设计概要','','MySQL 8、InnoDB、utf8mb4；20张业务表，统一 `id` 主键。公共账户/社区9表由黄奕浩负责，赛事/收藏11表由王乐之负责。前台采用 Yii2 Advanced MVC；公共身份和管理员基类保持原实现。','','## 20表数据字典']
for name,columns,indexes,foreign in tables:
    doc.extend(['',f'### {name}','','|字段|SQL定义|说明|','|---|---|---|'])
    for field,decl,comment in columns:doc.append(f'|`{field}`|`{decl}`|{comment}|')
    doc.extend(['', '索引：'+('；'.join(f'`{key}` ({fields})' for key,fields in indexes) or '仅主键')+'。'])
    doc.append('外键：'+('；'.join(f'`{field}` → `{target}.{key}`' for field,target,key in foreign) or '无')+'。')
doc.extend(['','## 统计口径','','- 小组积分按常规时间比分计：胜3、平1、负0；先比较积分、净胜球、进球数。若仍相同，需结合相互赛果及公平竞赛分进一步人工核对。','- 球队正式进球为常规时间加加时赛，不包含点球大战；乌龙球计入受益球队。','- 射手榜仅统计 `goal`、`penalty_goal`；不计乌龙球、点球大战命中。','- 图表查询每次从 MySQL 聚合；届次、阶段、球队筛选由服务器校验。','','## 数据来源与许可','','- 原始赛果与事件：OpenFootball 2022 数据，仓库声明 CC0-1.0。','- 球员阵容：stiles/world-cup 2022 `players.csv`，仓库代码 MIT，数据来自公开 FIFA API；使用时保留来源，并遵守 FIFA 数据再利用条款。','- 下载 URL、字节数和 SHA256 见 `data/raw/sources.json`。','- 4处原始赛程省略时间，沿用同日上一场时间，并在导入预览中显式提示。','','## Gii 与运行证据','','当前环境未提供 PHP、Yii Gii 可运行实例；模型初始代码由仓库内生成脚本产生，不能冒称 Gii 实际生成。需在课程环境运行 Gii 后补真实截图和修改记录。',''])
(root/'docs/绿茵数据团队_数据库与赛事模块设计(2413581_2411302).md').write_text('\n'.join(doc),encoding='utf-8')
print('tables',len(tables),'columns',sum(len(x[1]) for x in tables))
