<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '授权列表 - API接口';

$action2 = param(2);
empty($action2) and $action2 = '';

if ($action2 == 'add') {
	if ($method == 'GET') {
		include _include($haya_api_admin_view . '/app_add.htm');
	} else {
		$data = array();
		
		$data['name'] = param('name', '');
		if (empty($data['name'])) {
			message(-1, '授权名称不能为空');
		}
		$data['description'] = param('description', '');
		
		$data['app_id'] = md5(time().haya_api_rand_str(32));
		$data['app_secret'] = md5(time().haya_api_rand_str(32));
		
		$data['request_number'] = param('request_number', 1000);
		$data['allow_origin'] = param('allow_origin', 1);
		
		$data['status'] = param('status', 0);
		$data['create_date'] = time();
		$data['create_ip'] = $longip;
		
		$add_status = haya_api_app__create($data);
		if ($add_status === false) {
			message(-1, '添加授权失败');
		}
		
		message(0, jump('添加授权成功', url('haya_api-app')));
	}
} elseif ($action2 == 'edit') {
	if ($method == 'GET') {
		$id = param(3, 0);
		if ($id == 0) {
			message(-1, '授权ID错误');
		}
		
		$haya_api_app = haya_api_app__read($id);
		if (empty($haya_api_app)) {
			message(-1, '授权不存在');
		}
		
		include _include($haya_api_admin_view . '/app_edit.htm');
	} else {
		$data = array();
		
		$id = param('id', 0);
		if (empty($id)) {
			message(-1, '授权ID不能为空');
		}
		
		$data['name'] = param('name', '');
		if (empty($data['name'])) {
			message(-1, '授权名称不能为空');
		}
		$data['description'] = param('description', '');
		$data['request_number'] = param('request_number', 1000);
		$data['allow_origin'] = param('allow_origin', 1);
		$data['status'] = param('status', 0);
		
		$status = haya_api_app__update($id, $data);
		if ($status === false) {
			message(-1, '更新授权失败');
		}
		
		message(0, jump('更新授权成功', url('haya_api-app')));
	}
} elseif ($action2 == 'reset_secret') {
	if ($method != 'POST') {
		message(-1, '访问错误');
	}
	
	$data = array();
	
	$id = param('id', 0);
	if (empty($id)) {
		message(-1, '授权ID不能为空');
	}
	
	$data['app_secret'] = md5(time().haya_api_rand_str(32));
	
	$status = haya_api_app__update($id, $data);
	if ($status === false) {
		message(-1, '重置授权app_secret失败');
	}
	
	message(0, jump('重置授权app_secret成功', url('haya_api-app')));
} elseif ($action2 == 'delete') {
	if ($method != 'POST') {
		message(-1, '访问错误');
	}
	
	$id = param('id', 0);
	if (empty($id)) {
		message(-1, '授权ID不能为空');
	}
	
	$haya_api_app = haya_api_app__read($id);
	if (empty($haya_api_app)) {
		message(-1, '授权不存在');
	}
	
	$status = haya_api_app__delete($id);
	
	if ($status === false) {
		message(-1, '删除授权失败');
	}
	
	message(0, jump('删除授权成功', url('haya_api-app')));
} else {
	$pagesize = 10;
	$keyword  = trim(xn_urldecode(param(2)));
	$page     = param(3, 1);
	
	$where = array();
	if (!empty($keyword)) {
		$where['name'] = array('LIKE' => $keyword);
	}
	
	$total = haya_api_app__count($where);
	$haya_api_apps = haya_api_app_find($where, array('create_date' => -1, 'id' => -1), $page, $pagesize);
	
	$pagination = pagination(url("haya_api-app-{$keyword}-{page}"), $total, $page, $pagesize);

	include _include($haya_api_admin_view . '/app_index.htm');
}	

?>