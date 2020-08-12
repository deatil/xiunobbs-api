<?php

/**
 *          Name: 板块列表
 *           Url: haya_api-forum_list.htm
 *        Method: GET
 *          Body: {page, pagesize}
 *      Response: {code: 200, msg: '请求成功', data: {}}
 *   Description: 获取板块列表
 *
**/

defined('DEBUG') OR exit('Forbidden');

$page = haya_api_param('page', 1);
$pagesize = haya_api_param('pagesize', 20);
$pagesize = max(30, $pagesize);

$where = array();

$orderby = array(
    'rank' => -1,
);

// hook plugin_haya_api_forum_list_count_before.php

$total = forum_count($where);

// hook plugin_haya_api_forum_list_find_before.php

$list = forum_find($where, $orderby, $page, $pagesize);

// hook plugin_haya_api_forum_list_data_after.php

$data = array(
    'page' => $page,
    'pagesize' => $pagesize,
    'total' => $total,
    'list' => $list,
);

// hook plugin_haya_api_forum_list_end.php

haya_api_return_message(200, '获取成功', $data);

?>