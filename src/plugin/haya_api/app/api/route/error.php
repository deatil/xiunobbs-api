<?php

/**
 *      Name: 错误
 *       Url: haya_api-error.htm
 *    Method: GET
 *      Body: {}
 *  Response: {code: 500, msg: '访问错误', data: {}}
 *
**/

defined('DEBUG') OR exit('Forbidden');

haya_api_return_message(500, '访问错误');

?>