<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '用户登录 - API接口';

$action2 = param(2);
empty($action2) and $action2 = 'index';

if ($action2 == 'detail') {	

	if ($method != 'GET') {
		message(-1, jump('访问错误'));
	}
	
	$id = param(3, '');
	if (empty($id)) {
		message(-1, jump('授权不存在'));
	}
	
	$haya_api_session = haya_api_session__read($id);
	if (empty($haya_api_session)) {
		message(-1, jump('授权不存在'));
	}
	
	$haya_api_session_user = array();
	$haya_api_session_user = user__read($haya_api_session['uid']);
	if (empty($haya_api_session_user)) {
		$haya_api_session_user['username'] = '';
	}
	
	$haya_api_app = haya_api_app_read_by_app_id($haya_api_session['app_id']);
	if (empty($haya_api_app)) {
		$haya_api_app['name'] = null;
	}
	
	include _include($haya_api_admin_view.'/session_detail.htm');
} elseif ($action2 == 'delete') {
	if ($method != 'POST') {
		message(-1, '访问错误');
	}
	
	$id = param('id', 0);
	if (empty($id)) {
		message(-1, 'ID不能为空');
	}
	
	$haya_api_session = haya_api_session__read($id);
	if (empty($haya_api_session)) {
		message(-1, '用户登录授权不存在');
	}
	
	$status = haya_api_session__delete($id);
	
	if ($status === false) {
		message(-1, '删除用户登录授权失败');
	}
	
	message(0, jump('删除用户登录授权成功', url('haya_api-session')));
} elseif ($action2 == 'index') {
	if ($method != 'GET') {
		message(-1, jump('访问错误'));
	}

	$pagesize = 10;
	$srchtype = param(3);
	$keyword  = trim(xn_urldecode(param(4)));
	$page     = param(5, 1);
	
	if (!in_array($srchtype, array(
		'app_id', 
		'url', 
	))) {
		$srchtype = '';
	}
	
	$where = array();
	if (!empty($keyword)) {
		$where[$srchtype] = array('LIKE' => $keyword);
	}
	
	$total = haya_api_session__count($where);
	$haya_api_sessions = haya_api_session_find($where, array('create_date' => -1, 'id' => -1), $page, $pagesize);
	
	$pagination = pagination(url("haya_api-session-index-{$srchtype}-{$keyword}-{page}"), $total, $page, $pagesize);

	include _include($haya_api_admin_view.'/session_index.htm');
} else {
	message(-1, jump('访问错误'));
}

?>