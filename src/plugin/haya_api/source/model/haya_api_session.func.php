<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * API - 登录令牌
 * 
 * @author deatil
 * @create 2018-11-12
 */

function haya_api_session__create($arr) {
    $r = db_create('haya_api_session', $arr);
    return $r;
}

function haya_api_session__read($id) {
    $r = db_find_one('haya_api_session', array(
        'id' => $id,
    ));
    return $r;
}

function haya_api_session__find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('haya_api_session', $cond, $orderby, $page, $pagesize);
    
    return $r;
}

function haya_api_session__update($id, $arr) {
    $r = db_update('haya_api_session', array(
        'id' => $id,
    ), $arr);
    return $r;
}

function haya_api_session__delete($id) {
    if (empty($id)) {
        return false;
    }
    
    $r = db_delete('haya_api_session', array(
        'id' => $id
    ));
    return $r;
}

function haya_api_session__count($cond = array()) {
    $r = db_count('haya_api_session', $cond);
    return $r;
}

function haya_api_session_create($arr) {
    global $time, $longip;
    
    if (empty($arr)) {
        return false;
    }
    
    $agent = _SERVER('HTTP_USER_AGENT');
    $default_arr = array(
        'useragent'=> $agent,
        'token' => session_id(),
        'create_date' => $time,
        'create_ip' => $longip,
    );
    
    $arr = array_merge($default_arr, $arr);
    
    $r = haya_api_session__create($arr);
    return $r;
}

function haya_api_session_read_by_app_id($app_id) {
    $r = db_find_one('haya_api_session', array(
        'app_id' => $app_id,
    ));
    return $r;
}

function haya_api_session_read_by_app_id_and_uid($app_id, $uid) {
    $r = db_find_one('haya_api_session', array(
        'app_id' => $app_id,
        'uid' => $uid,
    ));
    return $r;
}

function haya_api_session_read_by_token($token) {
    $r = db_find_one('haya_api_session', array(
        'token' => $token,
    ));
    return $r;
}

function haya_api_session_find(
    $cond = array(), 
    $orderby = array(), 
    $page = 1, 
    $pagesize = 20
) {
    $r = db_find('haya_api_session', $cond, $orderby, $page, $pagesize);
    
    return $r;
}

?>