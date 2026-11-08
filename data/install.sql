-- World Cup database schema: 20 business tables.
SET NAMES utf8mb4;

CREATE TABLE `wc_sys_user` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `username` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录名',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '邮箱',
  `auth_key` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '身份密钥',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '密码哈希',
  `password_reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '重置令牌',
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '验证令牌',
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member' COMMENT '角色',
  `status` smallint NOT NULL DEFAULT '10' COMMENT '账户状态',
  `created_at` int NOT NULL COMMENT '创建时间戳',
  `updated_at` int NOT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户账户';

CREATE TABLE `wc_sys_team` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '团队名称',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '团队介绍',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='开发团队';

CREATE TABLE `wc_sys_member` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint NOT NULL COMMENT '账户编号',
  `team_id` bigint NOT NULL COMMENT '开发团队编号',
  `display_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '姓名',
  `student_number` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '学号',
  `bio` text COLLATE utf8mb4_unicode_ci COMMENT '个人介绍',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `wc_sys_member_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`),
  CONSTRAINT `wc_sys_member_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `wc_sys_team` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='开发成员';

CREATE TABLE `wc_content_announcement` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `author_id` bigint NOT NULL COMMENT '发布人',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '标题',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '正文',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT '发布状态',
  `published_at` datetime DEFAULT NULL COMMENT '发布时间',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  CONSTRAINT `wc_content_announcement_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `wc_sys_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='公告';

CREATE TABLE `wc_sys_assignment` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `owner_id` bigint NOT NULL COMMENT '所属用户',
  `scope` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'team或personal',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '文件名称',
  `relative_path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '相对路径',
  `sha256` char(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '摘要',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `owner_id` (`owner_id`),
  CONSTRAINT `wc_sys_assignment_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `wc_sys_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='作业文件';

CREATE TABLE `wc_sys_audit` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `actor_id` bigint NOT NULL COMMENT '操作人',
  `action` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '动作',
  `object_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '对象类型',
  `object_id` bigint DEFAULT NULL COMMENT '对象编号',
  `details` text COLLATE utf8mb4_unicode_ci COMMENT '操作摘要',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  CONSTRAINT `wc_sys_audit_ibfk_1` FOREIGN KEY (`actor_id`) REFERENCES `wc_sys_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='操作日志';

CREATE TABLE `wc_sys_notification` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint NOT NULL COMMENT '接收人',
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '主题',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '通知内容',
  `channel` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'file' COMMENT '投递渠道',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '投递状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `wc_sys_notification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='通知记录';

CREATE TABLE `wc_data_tournament` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `year` smallint NOT NULL COMMENT '年份',
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '赛事名称',
  `starts_at` date NOT NULL COMMENT '开始日期',
  `ends_at` date NOT NULL COMMENT '结束日期',
  `source_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `year` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='世界杯届次';

CREATE TABLE `wc_data_stage` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tournament_id` bigint NOT NULL COMMENT '届次编号',
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '阶段名称',
  `sort_order` int NOT NULL COMMENT '展示顺序',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournament_id` (`tournament_id`,`name`),
  CONSTRAINT `wc_data_stage_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `wc_data_tournament` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='赛事阶段';

CREATE TABLE `wc_data_group` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tournament_id` bigint NOT NULL COMMENT '届次编号',
  `name` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '小组名称',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournament_id` (`tournament_id`,`name`),
  CONSTRAINT `wc_data_group_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `wc_data_tournament` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='赛事小组';

CREATE TABLE `wc_data_stadium` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '球场名称',
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '城市',
  `capacity` int unsigned DEFAULT NULL COMMENT '容量',
  `source_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='球场';

CREATE TABLE `wc_data_team` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '队名',
  `fifa_code` char(3) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'FIFA代码',
  `confederation` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '洲足联',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT '介绍',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fifa_code` (`fifa_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='国家队';

CREATE TABLE `wc_data_player` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '球员姓名',
  `nationality_team_id` bigint NOT NULL COMMENT '国家队编号',
  `birth_date` date DEFAULT NULL COMMENT '出生日期',
  `position` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '位置',
  `source_url` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `nationality_team_id` (`nationality_team_id`),
  CONSTRAINT `wc_data_player_ibfk_1` FOREIGN KEY (`nationality_team_id`) REFERENCES `wc_data_team` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='球员';

CREATE TABLE `wc_data_squad` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tournament_id` bigint NOT NULL COMMENT '届次',
  `team_id` bigint NOT NULL COMMENT '国家队',
  `player_id` bigint NOT NULL COMMENT '球员',
  `group_id` bigint DEFAULT NULL COMMENT '所属小组',
  `shirt_number` smallint DEFAULT NULL COMMENT '球衣号码',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournament_id` (`tournament_id`,`team_id`,`player_id`),
  KEY `team_id` (`team_id`),
  KEY `player_id` (`player_id`),
  KEY `group_id` (`group_id`),
  CONSTRAINT `wc_data_squad_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `wc_data_tournament` (`id`),
  CONSTRAINT `wc_data_squad_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `wc_data_team` (`id`),
  CONSTRAINT `wc_data_squad_ibfk_3` FOREIGN KEY (`player_id`) REFERENCES `wc_data_player` (`id`),
  CONSTRAINT `wc_data_squad_ibfk_4` FOREIGN KEY (`group_id`) REFERENCES `wc_data_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='参赛阵容';

CREATE TABLE `wc_data_match` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `tournament_id` bigint NOT NULL COMMENT '届次',
  `stage_id` bigint NOT NULL COMMENT '阶段',
  `group_id` bigint DEFAULT NULL COMMENT '小组',
  `stadium_id` bigint NOT NULL COMMENT '球场',
  `match_number` int NOT NULL COMMENT '届内比赛编号',
  `home_team_id` bigint NOT NULL COMMENT '主队',
  `away_team_id` bigint NOT NULL COMMENT '客队',
  `kicks_off_at` datetime NOT NULL COMMENT 'UTC开赛时间',
  `normal_home` smallint unsigned DEFAULT NULL COMMENT '常规主队进球',
  `normal_away` smallint unsigned DEFAULT NULL COMMENT '常规客队进球',
  `extra_home` smallint unsigned DEFAULT NULL COMMENT '加时主队进球',
  `extra_away` smallint unsigned DEFAULT NULL COMMENT '加时客队进球',
  `penalty_home` smallint unsigned DEFAULT NULL COMMENT '点球大战主队命中',
  `penalty_away` smallint unsigned DEFAULT NULL COMMENT '点球大战客队命中',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'scheduled' COMMENT '比赛状态',
  `source_key` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源去重键',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `tournament_id` (`tournament_id`,`match_number`),
  UNIQUE KEY `source_key` (`source_key`),
  KEY `stage_id` (`stage_id`),
  KEY `group_id` (`group_id`),
  KEY `stadium_id` (`stadium_id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  CONSTRAINT `wc_data_match_ibfk_1` FOREIGN KEY (`tournament_id`) REFERENCES `wc_data_tournament` (`id`),
  CONSTRAINT `wc_data_match_ibfk_2` FOREIGN KEY (`stage_id`) REFERENCES `wc_data_stage` (`id`),
  CONSTRAINT `wc_data_match_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `wc_data_group` (`id`),
  CONSTRAINT `wc_data_match_ibfk_4` FOREIGN KEY (`stadium_id`) REFERENCES `wc_data_stadium` (`id`),
  CONSTRAINT `wc_data_match_ibfk_5` FOREIGN KEY (`home_team_id`) REFERENCES `wc_data_team` (`id`),
  CONSTRAINT `wc_data_match_ibfk_6` FOREIGN KEY (`away_team_id`) REFERENCES `wc_data_team` (`id`),
  CONSTRAINT `wc_data_match_chk_1` CHECK ((`home_team_id` <> `away_team_id`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='比赛';

CREATE TABLE `wc_data_event` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `match_id` bigint NOT NULL COMMENT '比赛编号',
  `player_id` bigint DEFAULT NULL COMMENT '球员',
  `team_id` bigint NOT NULL COMMENT '所属队',
  `minute` smallint unsigned NOT NULL COMMENT '分钟',
  `added_minute` smallint unsigned NOT NULL DEFAULT '0' COMMENT '补时分钟',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '事件类型',
  `source_key` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '来源去重键',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `source_key` (`source_key`),
  KEY `match_id` (`match_id`),
  KEY `player_id` (`player_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `wc_data_event_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `wc_data_match` (`id`),
  CONSTRAINT `wc_data_event_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `wc_data_player` (`id`),
  CONSTRAINT `wc_data_event_ibfk_3` FOREIGN KEY (`team_id`) REFERENCES `wc_data_team` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='比赛事件';

CREATE TABLE `wc_fan_post` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint NOT NULL COMMENT '作者',
  `match_id` bigint DEFAULT NULL COMMENT '关联比赛',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '话题标题',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '话题内容',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '审核状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `wc_fan_post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`),
  CONSTRAINT `wc_fan_post_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `wc_data_match` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='球迷话题';

CREATE TABLE `wc_fan_comment` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `post_id` bigint NOT NULL COMMENT '话题编号',
  `user_id` bigint NOT NULL COMMENT '作者',
  `parent_id` bigint DEFAULT NULL COMMENT '回复对象',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '评论内容',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT '审核状态',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `wc_fan_comment_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `wc_fan_post` (`id`),
  CONSTRAINT `wc_fan_comment_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`),
  CONSTRAINT `wc_fan_comment_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `wc_fan_comment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='话题评论及回复';

CREATE TABLE `wc_fan_team_favorite` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint NOT NULL COMMENT '用户',
  `team_id` bigint NOT NULL COMMENT '球队',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`team_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `wc_fan_team_favorite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`),
  CONSTRAINT `wc_fan_team_favorite_ibfk_2` FOREIGN KEY (`team_id`) REFERENCES `wc_data_team` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='球队收藏';

CREATE TABLE `wc_fan_match_favorite` (
  `id` bigint NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` bigint NOT NULL COMMENT '用户',
  `match_id` bigint NOT NULL COMMENT '比赛',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`match_id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `wc_fan_match_favorite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `wc_sys_user` (`id`),
  CONSTRAINT `wc_fan_match_favorite_ibfk_2` FOREIGN KEY (`match_id`) REFERENCES `wc_data_match` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='比赛收藏';
