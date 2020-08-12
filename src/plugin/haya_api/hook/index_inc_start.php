<?php exit;

if (!defined('MESSAGE_HTM_PATH')) {
    $haya_api_route = param(0);

    if ($haya_api_route == 'haya_api') {
        $haya_api_get = $_GET;
        $haya_api_post = $_POST;
        
        $sid = sess_start();

        // 语言 / Language
        $_SERVER['lang'] = $lang = include _include(APP_PATH."lang/$conf[lang]/bbs.php");

        // 用户组 / Group
        $grouplist = group_list_cache();

        // 转换为绝对路径，防止被包含时出错。
        substr($conf['log_path'], 0, 2) == './' AND $conf['log_path'] = APP_PATH.$conf['log_path']; 
        substr($conf['tmp_path'], 0, 2) == './' AND $conf['tmp_path'] = APP_PATH.$conf['tmp_path']; 
        substr($conf['upload_path'], 0, 2) == './' AND $conf['upload_path'] = APP_PATH.$conf['upload_path']; 

        $_SERVER['conf'] = $conf;
        
        $forumlist = forum_list_cache();
        // 有权限查看的板块 / filter no permission forum
        $forumlist_show = forum_list_access_filter($forumlist, $gid);
        $forumarr = arrlist_key_values($forumlist_show, 'fid', 'name');

        // app信息签名验证
        $api_app = haya_api_app_check();
        
        // 接口appid
        $app_id = $api_app['app_id'];

        // 用户组
        $grouplist = group_list_cache();

        // 用户信息
        $uid = haya_api_user_token_get();
        $user = user_read($uid);

        // 用户所属用户组信息
        $gid = empty($user) ? 0 : intval($user['gid']);
        $group = isset($grouplist[$gid]) ? $grouplist[$gid] : $grouplist[0];

        include _include(APP_PATH.'plugin/haya_api/app/api/route.php'); 
        exit;
    }
}

?>