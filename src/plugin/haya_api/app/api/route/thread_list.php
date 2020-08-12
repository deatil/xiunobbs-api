<?php

/**
 *      Name: 帖子列表
 *       Url: haya_api-thread_list.htm
 *    Method: GET
 *      Body: {start, limit, uid, ordertype, order}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '请求错误');
}

$fid = haya_api_param('fid', 0);
$page = haya_api_param('page', 1);
$orderby = haya_api_param('orderby');

!in_array($orderby, array('tid', 'lastpid')) AND $orderby = 'lastpid';

forum_access_user($fid, $gid, 'allowread') OR haya_api_return_message(405, lang('insufficient_visit_forum_privilege'));
$pagesize = $conf['pagesize'];

$where = array(
    'fid' => $fid,
);

$total = thread_count($where);

$list = thread_find_by_fid($fid, $page, $pagesize, $orderby);

$data = array(
    'page' => $page,
    'pagesize' => $pagesize,
    'total' => $total,
    'list' => $list,
);

// hook plugin_haya_api_thread_list_end.php

haya_api_return_message(200, '获取成功', $data);

?>