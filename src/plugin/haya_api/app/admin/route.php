<?php

/**
 * API
 * 
 * @create 2018-11-5
 * @author deatil
 * 
 * @license GPL2 
 */
 
defined('DEBUG') OR exit('Forbidden');

// 动作
$action = param(1);
empty($action) and $action = 'setting';

$actions = array(
	'setting',
	'api',
	'app',
	'session',
	'user_log',
	'log',
	'request_log',
	'code',
	'sign', // 签名说明
);

if (!in_array($action, $actions)) {
	message(-1, jump('访问错误'));
}

$haya_api_admin_action_file = APP_PATH.'plugin/haya_api/app/admin/route/'.$action.'.php';
if (!file_exists($haya_api_admin_action_file)) {
	message(-1, jump('路由文件不存在'));
}

// 定义导航
$haya_api_admin_config = APP_PATH.'plugin/haya_api/app/admin/config/admin_menu.php';
if (!file_exists($haya_api_admin_config)) {
	message(-1, jump('插件配置文件不存在', url('plugin')));
}
$haya_api_admin_menu = include _include($haya_api_admin_config);

$tablepre = $db->tablepre;
$haya_api_admin_view = APP_PATH.'plugin/haya_api/app/admin/view/htm';

include _include($haya_api_admin_action_file);

