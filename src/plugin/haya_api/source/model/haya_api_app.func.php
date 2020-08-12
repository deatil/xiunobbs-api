<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * API - app
 * 
 * @author deatil
 * @create 2018-11-4
 */

function haya_api_app__create($arr) {
	$r = db_create('haya_api_app', $arr);
	return $r;
}

function haya_api_app__read($id) {
	$r = db_find_one('haya_api_app', array(
		'id' => $id,
	));
	return $r;
}

function haya_api_app__find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_app', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

function haya_api_app__update($id, $arr) {
	$r = db_update('haya_api_app', array(
		'id' => $id,
	), $arr);
	return $r;
}

function haya_api_app__delete($id) {
	if (empty($id)) {
		return false;
	}
	
	$r = db_delete('haya_api_app', array(
		'id' => $id
	));
	return $r;
}

function haya_api_app__count($cond = array()) {
	$r = db_count('haya_api_app', $cond);
	return $r;
}

function haya_api_app_read_by_app_id($app_id) {
	$r = db_find_one('haya_api_app', array(
		'app_id' => $app_id,
	));
	return $r;
}

function haya_api_app_find(
	$cond = array(), 
	$orderby = array(), 
	$page = 1, 
	$pagesize = 20
) {
	$r = db_find('haya_api_app', $cond, $orderby, $page, $pagesize);
	
	return $r;
}

?>