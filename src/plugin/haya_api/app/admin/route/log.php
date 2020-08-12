<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '接口日志 - API接口';

$action2 = param(2);
empty($action2) and $action2 = 'index';

if ($action2 == 'detail') {	

	if ($method != 'GET') {
		message(-1, jump('访问错误'));
	}
	
	$log_id = param(3, '');
	if (empty($log_id)) {
		message(-1, jump('日志不存在'));
	}
	
	$haya_api_log = haya_api_log__read($log_id);
	if (empty($haya_api_log)) {
		message(-1, jump('日志不存在'));
	}
	
	$haya_api_app = haya_api_app_read_by_app_id($haya_api_log['app_id']);
	if (empty($haya_api_app)) {
		$haya_api_app['name'] = null;
	}
	
	include _include($haya_api_admin_view.'/log_detail.htm');
} elseif ($action2 == 'delete') {
	if ($method != 'POST') {
		message(-1, '访问错误');
	}
	
	$id = param('id', 0);
	if (empty($id)) {
		message(-1, '日志ID不能为空');
	}
	
	$haya_api_log = haya_api_log__read($id);
	if (empty($haya_api_log)) {
		message(-1, '日志不存在');
	}
	
	$status = haya_api_log__delete($id);
	
	if ($status === false) {
		message(-1, '删除日志失败');
	}
	
	message(0, jump('删除日志成功', url('haya_api-log')));
} elseif ($action2 == 'index') {
	if ($method != 'GET') {
		message(-1, jump('访问错误'));
	}

	$pagesize = 20;
	$srchtype = param(3);
	$keyword  = trim(xn_urldecode(param(4)));
	$page     = param(5, 1);
	
	if (!in_array($srchtype, array(
		'app_id', 
		'url', 
		'method', 
	))) {
		$srchtype = '';
	}
	
	$where = array();
	if (!empty($keyword)) {
		$where[$srchtype] = array('LIKE' => $keyword);
	}
	
	$total = haya_api_log__count($where);
	$haya_api_logs = haya_api_log_find($where, array('create_date' => -1, 'id' => -1), $page, $pagesize);
	
	$pagination = pagination(url("haya_api-log-index-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

	include _include($haya_api_admin_view.'/log_index.htm');
} else {
	message(-1, jump('访问错误'));
}

?>