<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * 用户信息
 * 
 * @author deatil
 * @create 2018-11-15
 */
function haya_api_user_single_read($uid) {
    $haya_api_config = kv_get('haya_api');
    
    $post_user = user_read($uid);
    $r = array(
        'uid' => $post_user['uid'],
        'username' => $post_user['username'],
        'realname' => $post_user['realname'],
        'avatar_url' => $haya_api_config['api_domain'] . $post_user['avatar_url'],
        'gid' => $post_user['gid'],
        'groupname' => $post_user['groupname'],
        'online_status' => $post_user['online_status'],
    );
    return $r;
}

/**
 * 用户信息
 * 
 * @author deatil
 * @create 2018-11-18
 */
function haya_api_user_read($uid, $unsets = array()) {
    $haya_api_config = kv_get('haya_api');
    
    $r = user_read($uid);
    unset($r['password'], $r['password_sms'], $r['salt']);
    
    if (!empty($unsets) && is_array($unsets)) {
        foreach ($unsets as $unset) {
            unset($r[$unset]);
        }
    }
    
    if (isset($r['avatar_url'])) {
        $r['avatar_url'] = $haya_api_config['api_domain'] . $r['avatar_url'];
    }
    
    return $r;
}

/**
 * 用户是否登陆检测
 * 
 * @author deatil
 * @create 2020-7-15
 */
function haya_api_user_login_check() {
    global $user;

    // hook plugin_haya_api_model_user_login_check_start.php

    if (empty($user)) {
        haya_api_return_message(404, '你还没有登陆');
    }

    // hook plugin_haya_api_model_user_login_check_end.php
}

/**
 * 用户登陆token
 * 
 * @author deatil
 * @create 2020-7-15
 */
function haya_api_user_token_get() {
    $token = haya_api_param('token');
    if (empty($token)) {
        return false;
    }
    
    $user_session = haya_api_session_read_by_token($token);
    if (empty($user_session)
        || $user_session['logout'] != 0
    ) {
        return false;
    }
    
    return $user_session['uid'];

}

?>