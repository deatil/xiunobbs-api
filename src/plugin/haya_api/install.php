<?php

/**
 * API
 * 
 * @author deatil
 * @create 2018-11-4
 */

defined('DEBUG') OR exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "
CREATE TABLE {$tablepre}haya_api_app (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL DEFAULT '' COMMENT 'APP名称',
    `description` varchar(250) DEFAULT '' COMMENT '描述',
    `app_id` varchar(32) NOT NULL DEFAULT '' COMMENT 'APP的ID',
    `app_secret` varchar(50) NOT NULL DEFAULT '' COMMENT 'APP的SECRET',
    `request_number` int(10) NULL DEFAULT '1000' COMMENT '最大请求次数',
    `is_apply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1-自主申请',
    `allow_origin` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-允许外部访问',
    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，1-启用',
    `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
    `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
    PRIMARY KEY (`id`),
    KEY `app_id_app_secret` (`app_id`, `app_secret`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
db_exec($sql);

$sql = "
CREATE TABLE {$tablepre}haya_api_log (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `app_id` varchar(32) NOT NULL DEFAULT '' COMMENT '请求的APP_ID',
    `api_url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求API',
    `url` text NOT NULL DEFAULT '' COMMENT '请求链接',
    `method` varchar(10) NOT NULL DEFAULT '' COMMENT '请求方法',
    `body` text DEFAULT '' COMMENT '请求实体，可为空',
    `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
    `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
    PRIMARY KEY (`id`),
    KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
db_exec($sql);

$sql = "
CREATE TABLE {$tablepre}haya_api_request_log (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `app_id` varchar(32) NOT NULL DEFAULT '' COMMENT '请求的APP_ID',
    `api_url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求API',
    `method` varchar(10) NOT NULL DEFAULT '' COMMENT '请求方法',
    `request_ip` int(10) NOT NULL DEFAULT '0' COMMENT '请求IP',
    `request_count` int(10) NULL DEFAULT '0' COMMENT '请求次数',
    `last_active` int(10) NULL DEFAULT '0' COMMENT '上次活动时间',
    `last_ip` int(10) NULL DEFAULT '0' COMMENT '上次活动IP',
    `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
    `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
    PRIMARY KEY (`id`),
    KEY `app_id` (`app_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
db_exec($sql);

$sql = "
CREATE TABLE {$tablepre}haya_api_session (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL DEFAULT '0' COMMENT '登录UID',
    `app_id` varchar(32) NOT NULL DEFAULT '' COMMENT '请求的APP_ID',
    `token` varchar(32) NOT NULL DEFAULT '' COMMENT '请求令牌',
    `useragent` varchar(128) NOT NULL DEFAULT '' COMMENT '请求头',
    `logins` int(10) NULL DEFAULT '1' COMMENT '登录次数',
    `logout` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，1-已退出',
    `logout_date` int(10) NULL DEFAULT '0' COMMENT '退出时间',
    `logout_ip` int(10) NULL DEFAULT '0' COMMENT '退出IP',
    `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
    `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
    PRIMARY KEY (`id`),
    KEY `app_id` (`app_id`),
    KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
db_exec($sql);

$sql = "
CREATE TABLE {$tablepre}haya_api_user_log (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `uid` int(10) NOT NULL DEFAULT '0' COMMENT '登录UID',
    `app_id` varchar(32) NOT NULL DEFAULT '' COMMENT '请求的APP_ID',
    `token` varchar(32) NOT NULL DEFAULT '' COMMENT '请求令牌',
    `useragent` varchar(128) NOT NULL DEFAULT '' COMMENT '请求头',
    `type` varchar(10) DEFAULT 'login' COMMENT '日志类型',
    `create_date` int(10) NULL DEFAULT '0' COMMENT '添加时间',
    `create_ip` int(10) NULL DEFAULT '0' COMMENT '添加IP',
    PRIMARY KEY (`id`),
    KEY `uid_app_id` (`uid`, `app_id`),
    KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
db_exec($sql);

// 添加插件配置
$haya_api_config = array(
    "api_domain" => '',
    
    "is_add_log_body" => 1,
    
    "api_close" => 0,
    "api_close_tip" => 'API维护中~',
);
kv_set('haya_api', $haya_api_config); 

// 接口列表缓存
kv_set('haya_api_apis', array()); 

?>