<?php

/**
 * 错误代码
 *
 * @create 2018-11-18
 * @author deatil
 */

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = '错误代码 - API接口';

// hook haya_api_admin_code_start.php

$haya_api_error_codes = array(
	'200' => array(
		'code' => 200,
		'name' => '获取成功',
		'description' => '获取数据成功',
	),
	'404' => array(
		'code' => 404,
		'name' => '获取失败',
		'description' => '获取数据失败，包括签名填写信息错误等',
	),
	'405' => array(
		'code' => 405,
		'name' => '登录失败',
		'description' => '登录失败、注册失败',
	),
	'500' => array(
		'code' => 500,
		'name' => '内部错误',
		'description' => 'API不存在、请求方式错误或者API关闭等',
	),
	'501' => array(
		'code' => 501,
		'name' => '签名错误',
		'description' => 'API签名验证错误',
	),
);

// hook haya_api_admin_code_end.php

include _include($haya_api_admin_view.'/code.htm');

?>