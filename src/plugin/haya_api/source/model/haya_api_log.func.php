<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * API - 日志
 * 
 * @author deatil
 * @create 2018-11-4
 */

function haya_api_log__create($arr) {
	$r = db_create('haya_api_log', $arr);
	return $r;
}

function haya_api_log__read($id) {
	$r = db_find_one('haya_api_log', array(
		'id' => $id,
	));
	return $r;
}

function haya_api_log__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_log', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_api_log__update($id, $arr) {
	$r = db_update('haya_api_log', array(
		'id' => $id,
	), $arr);
	return $r;
}

function haya_api_log__delete($id) {
	if (empty($id)) {
		return false;
	}
	
	$r = db_delete('haya_api_log', array(
		'id' => $id
	));
	return $r;
}

function haya_api_log__count($cond = array()) {
	$r = db_count('haya_api_log', $cond);
	return $r;
}

function haya_api_log_create($arr) {
	global $time, $longip;
	
	if (empty($arr)) {
		return false;
	}
	
	$default_arr = array(
		'create_date' => $time,
		'create_ip' => $longip,
	);
	
	$arr = array_merge($default_arr, $arr);
	
	$r = haya_api_log__create($arr);
	return $r;
}

function haya_api_log_read_by_app_id($app_id) {
	$r = db_find_one('haya_api_log', array(
		'app_id' => $app_id,
	));
	return $r;
}

function haya_api_log_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_log', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

?>