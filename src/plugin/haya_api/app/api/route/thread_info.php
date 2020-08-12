<?php

/**
 *      Name: 帖子详情
 *       Url: haya_api-thread_info.htm
 *    Method: GET
 *      Body: {tid}
 *  Response: {code: 200, msg: '请求成功', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

if ($method != 'GET') {
    haya_api_return_message(500, '访问错误');
}

$tid = haya_api_param('tid');
if (empty($tid)) {
    haya_api_return_message(404, '帖子ID不能为空');
}

// hook plugin_haya_api_thread_info_thread_read_before.php

$thread = thread_read($tid);
if (empty($thread)) {
    haya_api_return_message(404, lang('thread_not_exists'));
}

// hook plugin_haya_api_thread_info_post_read_before.php

$first = post_read($thread['firstpid']);

// hook plugin_haya_api_thread_info_message_before.php

$thread['message'] = $first['message_fmt'];

// hook plugin_haya_api_thread_info_inc_views_before.php

thread_inc_views($tid);

$data = $thread;

// hook plugin_haya_api_thread_info_end.php

haya_api_return_message(200, '获取成功', $data);


?>