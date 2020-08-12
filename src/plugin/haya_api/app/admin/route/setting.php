<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '设置 - API接口';

if ($method == 'GET') {
	
	$config = kv_get('haya_api');
	
	include _include($haya_api_admin_view.'/setting.htm');
	
} else {
	
	$config = array();
	
	$config['api_domain'] = param('api_domain', '');
	
	$config['is_add_log_body'] = param('is_add_log_body', 0);
	
	$config['api_close'] = param('api_close', 0);
	$config['api_close_tip'] = param('api_close_tip', '');
	if ($config['api_close'] == 1 && empty($config['api_close_tip'])) {
		message(-1, jump('API关闭维护时，请填写关闭维护原因'));
	}
	
	kv_set('haya_api', $config); 
	
	$clear_apis_cache = param('clear_apis_cache', 0);
	if ($clear_apis_cache == 1) {
		kv_delete('haya_api_apis'); 
	}
	
	message(0, jump('设置保存成功', url('haya_api-setting')));
}

?>