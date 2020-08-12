<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * API - 登录日志
 * 
 * @author deatil
 * @create 2018-11-22
 */

function haya_api_user_log__create($arr) {
	$r = db_create('haya_api_user_log', $arr);
	return $r;
}

function haya_api_user_log__read($id) {
	$r = db_find_one('haya_api_user_log', array(
		'id' => $id,
	));
	return $r;
}

function haya_api_user_log__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_user_log', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_api_user_log__update($id, $arr) {
	$r = db_update('haya_api_user_log', array(
		'id' => $id,
	), $arr);
	return $r;
}

function haya_api_user_log__delete($id) {
	if (empty($id)) {
		return false;
	}
	
	$r = db_delete('haya_api_user_log', array(
		'id' => $id
	));
	return $r;
}

function haya_api_user_log__count($cond = array()) {
	$r = db_count('haya_api_user_log', $cond);
	return $r;
}

function haya_api_user_log_create($arr) {
	global $time, $longip;
	
	if (empty($arr)) {
		return false;
	}
	
	$agent = _SERVER('HTTP_USER_AGENT');
	$default_arr = array(
		'useragent'=> $agent,
		'create_date' => $time,
		'create_ip' => $longip,
	);
	
	$arr = array_merge($default_arr, $arr);
	
	$r = haya_api_user_log__create($arr);
	return $r;
}

function haya_api_user_log_read_by_app_id($app_id) {
	$r = db_find_one('haya_api_user_log', array(
		'app_id' => $app_id,
	));
	return $r;
}

function haya_api_user_log_read_by_app_id_and_uid($app_id, $uid) {
	$r = db_find_one('haya_api_user_log', array(
		'app_id' => $app_id,
		'uid' => $uid,
	));
	return $r;
}

function haya_api_user_log_read_by_token($token) {
	$r = db_find_one('haya_api_user_log', array(
		'token' => $token,
	));
	return $r;
}

function haya_api_user_log_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_user_log', $cond, $orderby, $page, $pagesize);
	if (!empty($r)) {
		foreach ($r as & $_r) {
			$_r['user'] = haya_api_user_single_read($_r['uid']);
		}
	}
	
	return $r;
}

?>