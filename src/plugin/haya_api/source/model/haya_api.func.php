<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * 公用函数
 *
 * @create 2018-11-12
 * @author deatil
 */

function haya_api_admin_tab_active($arr, $active) {
    $s = '';
    foreach ($arr as $k => $v) {
        if (isset($v['class'])) {
            $s .= '<a role="button" class="btn btn-secondary '.$v['class'].($active == $k ? ' active' : '').'" href="'.$v['url'].'">'.$v['text'].'</a>';
        } else {
            $s .= '<a role="button" class="btn btn-secondary'.($active == $k ? ' active' : '').'" href="'.$v['url'].'">'.$v['text'].'</a>';
        }
    }
    return $s;
}

/**
 * 配置
 *
 * @author deatil
 * @create 2020-7-15
 */
function haya_api_config($key = '', $default = '') {
    static $config;
    
    // hook plugin_haya_api_config_start.php
    
    if (empty($config)) {
        $config = kv_get('haya_api');
    }
    
    // hook plugin_haya_api_get_config_after.php
    
    if (empty($key)) {
        return $config;
    }
    
    // hook plugin_haya_api_config_end.php
    
    if (isset($config[$key])) {
        return !empty($config[$key]) ? $config[$key] : $default;
    }
    
    return $default;
}


/**
 * app验证
 * 
 * @author deatil
 * @create 2019-3-2
 */
function haya_api_app_check() {
    global $haya_api_get, $method;

    // 数据格式化
    if ($method == 'GET') {
        $app_data = $haya_api_get;
        $app_post_data = '';
    } else {
        $app_post_data = file_get_contents('php://input');
        $app_data = haya_api_json_decode($app_post_data);
        
        if (empty($app_data)) {
            haya_api_return_message(501, '请求数据错误');
        }
    }

    // 记录日志
    haya_api_create_log($app_data['app_id'], $method, $app_post_data);

    // 签名验证
    if (!isset($app_data['app_id']) 
        || empty($app_data['app_id'])
    ) {
        haya_api_return_message(501, '签名错误');
    }

    if (!isset($app_data['nonce_str']) 
        || empty($app_data['nonce_str'])
    ) {
        haya_api_return_message(501, '签名错误');
    }

    if (strlen($app_data['nonce_str']) != 16) {
        haya_api_return_message(501, '签名错误');
    }

    if (strlen($app_data['nonce_str']) != 16) {
        haya_api_return_message(501, '签名错误');
    }
    
    $timestamp = $app_data['timestamp'];
    if (empty($timestamp)) {
        haya_api_return_message(501, "时间戳错误");
    }
    if (strlen($timestamp) != 10) {
        haya_api_return_message(501, "时间戳格式错误");
    }
    if (time() - $timestamp > (60 * 30)) {
        haya_api_return_message(501, "时间错误，请确认你的时间为正确的北京时间");
    }

    $app_id = $app_data['app_id'];

    $api_app = haya_api_app_read_by_app_id($app_id);
    if (empty($api_app)) {
        haya_api_return_message(501, '授权错误');
    }

    if ($api_app['status'] != 1){
        haya_api_return_message(501, '授权不存在或者被禁用');
    }

    $check_sign = haya_api_check_sign($app_data, $api_app['app_secret']);
    if ($check_sign == false) {
        haya_api_return_message(501, '授权失败');
    }

    $haya_api_config = kv_get('haya_api');
    if ($haya_api_config['api_close'] == 1) {
        haya_api_return_message(501, $haya_api_config['api_close_tip']);
    }

    if ($api_app['allow_origin'] == 1) {
        header('Content-Type:text/html; charset=utf-8');
        header('Access-Control-Allow-Origin:*');  
        header('Access-Control-Allow-Methods:POST');  
        header('Access-Control-Allow-Headers:x-requested-with,content-type');	
    }

    return $api_app;
}

include_once APP_PATH.'plugin/haya_api/source/class/sign.php';

/**
 * 验证签名
 * 
 * @author deatil
 * @create 2018-11-4
 */
function haya_api_check_sign($data = array(), $key = '') {
    $sign = new haya_api_sign();
    return $sign->check_sign($data, $key);
}

/**
 * 生成签名
 * 
 * @author deatil
 * @create 2018-11-4
 */
function haya_api_get_sign($data = array(), $key = '') {
    $sign = new haya_api_sign();
    return $sign->get_sign($data, $key);
}

/**
 * 解析URL
 * 
 * @author deatil
 * @create 2018-11-26
 */
function haya_api_urldecode($s) {
    $s = str_replace('_', '%', $s);
    $s = urldecode($s);
    return $s; 
}

/**
 * json中文输出
 * 
 * @author deatil
 * @create 2018-11-26
 */
function haya_api_json_encode($data, $pretty = FALSE, $level = 0) {
    if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
        return json_encode($data, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
    
    $tab = $pretty ? str_repeat("\t", $level) : '';
    $tab2 = $pretty ? str_repeat("\t", $level + 1) : '';
    $br = $pretty ? "\r\n" : '';
    switch ($type = gettype($data)) {
        case 'NULL':
            return 'null';
        case 'boolean':
            return ($data ? 'true' : 'false');
        case 'integer':
        case 'double':
        case 'float':
            return $data;
        case 'string':
            $data = '"'.str_replace(array('\\', '"'), array('\\\\', '\\"'), $data).'"';
            $data = str_replace("\r", '\\r', $data);
            $data = str_replace("\n", '\\n', $data);
            $data = str_replace("\t", '\\t', $data);
            return $data;
        case 'object':
            $data = get_object_vars($data);
        case 'array':
            $output_index_count = 0;
            $output_indexed = array();
            $output_associative = array();
            foreach ($data as $key => $value) {
                $output_indexed[] = haya_api_json_encode($value, $pretty, $level + 1);
                $output_associative[] = $tab2.'"'.$key.'":' . haya_api_json_encode($value, $pretty, $level + 1);
                if ($output_index_count !== NULL && $output_index_count++ !== $key) {
                    $output_index_count = NULL;
                }
            }
            if ($output_index_count !== NULL) {
                return '[' . implode(",$br", $output_indexed) . ']';
            } else {
                return "{{$br}" . implode(",$br", $output_associative) . "{$br}{$tab}}";
            }
        default:
            return ''; // Not supported
    }
}

/**
 * json解析
 * 
 * @author deatil
 * @create 2018-11-26
 */
function haya_api_json_decode($json) {
    $json = trim($json, "\xEF\xBB\xBF");
    $json = trim($json, "\xFE\xFF");
    return json_decode($json, 1);
}

/**
 * 返回接口信息
 * 
 * code - 200, 404, 500
 * 
 * @author deatil
 * @create 2018-11-12
 */
function haya_api_return_message($code = 200, $msg = null, $data = array()) {
    ob_clean();
    header('Content-Type:application/json; charset=utf-8');
    exit(haya_api_json_encode(array(
        'code' => $code,
        'msg' => $msg,
        'data' => $data,
    )));
}

/**
 * 生成随机字符串
 * @param int $length
 * @return string
 *
 * @create 2018-11-8
 * @author deatil
 */
function haya_api_rand_str($length = 32)
{
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
}

/**
 * 获取API请求数据
 * 
 * @author deatil
 * @create 2018-12-5
 */
function haya_api_param($key, $defval = '', $return_all = false) {
    global $haya_api_get;
    
    $method = strtoupper($_SERVER['REQUEST_METHOD']);
    
    // 数据格式化
    if ($method == 'GET') {
        $app_data = $haya_api_get;
    } else {
        $app_post_data = file_get_contents('php://input');
        $app_data = haya_api_json_decode($app_post_data);
    }
    
    if ($return_all) {
        return $app_data;
    }
    
    if (empty($app_data)
        || !isset($app_data[$key]) 
        || ($key === 0 && empty($app_data[$key]))
    ) {
        if (is_array($defval)) {
            return array();
        } else {
            return $defval;
        }
    }
    
    $val = $app_data[$key];
    return $val;
}

/**
 * 记录日志
 * 
 * @author deatil
 * @create 2020-3-1
 */
function haya_api_create_log($app_id, $method, $data = null) {
    global $haya_api_config, $conf, $time, $longip;
    
    if (!isset($app_id) || empty($app_id)) {
        $app_id = 0;
    }
    
    if ($haya_api_config['is_add_log_body'] == 1) {
        $haya_api_post_body = var_export($data, true);
    } else {
        $haya_api_post_body = '';
    }

    $haya_api_request_uri_no_path = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
    $request_uri = parse_url(trim($haya_api_request_uri_no_path));
    if ($conf['url_rewrite_on'] == 0) {
        $request_uri_query = $request_uri['query'];
        $request_uri_query_arr = explode('&', $request_uri_query);
        $api_url = $request_uri_query_arr[0];
    } else {
        $api_url = $request_uri['path'];
    }
    
    // 记录请求日志
    $api_app = haya_api_app_read_by_app_id($app_id);
    if ($api_app['request_number'] > 0) {
        $request_log = haya_api_request_log_read_by_app_id_and_api_url_and_request_ip($app_id, $api_url, $longip);

        if (!empty($request_log)) {
            // 检测
            if ($request_log['last_active'] <= $time
                && ($request_log['last_active'] + 60) >= $time
            ) {
                if ($request_log['request_count'] >= $api_app['request_number']) {
                    haya_api_return_message(404, '访问过快');
                }
            }
            
            if ($request_log['last_active'] <= $time
                && ($request_log['last_active'] + 60) >= $time
            ) {
                haya_api_request_log_update(array(
                    'app_id' => $app_id,
                    'api_url' => $api_url,
                    'request_ip' => $longip,
                ), array(
                    'request_count+' => 1,
                ));
            } else {
                if ($request_log['request_count'] < $api_app['view_page_num']
                    || (
                        $request_log['request_count'] >= $api_app['request_number']
                        && ($request_log['last_active'] + 60) < $time
                    )
                ) {
                    // 重置统计
                    haya_api_request_log_update(array(
                        'app_id' => $app_id,
                        'api_url' => $api_url,
                        'request_ip' => $longip,
                    ), array(
                        'request_count' => 1,
                        'last_active' => $time,
                        'last_ip' => $longip,
                    ));
                }
            }
        } else {
            haya_api_request_log_create(array(
                'app_id' => $app_id,
                'api_url' => $api_url,
                'method' => strtoupper($method),
                'request_ip' => $longip,
                'request_count' => 1,
                'last_active' => $time,
                'last_ip' => $longip,
            ));
        }
    }
    
    $is_https = is_https();
    $url = 'http';
    if ($is_https) {
        $url .= 's';
    }
    $url .= '://' . $_SERVER['HTTP_HOST'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER['REQUEST_URI'];

    return haya_api_log_create(array(
        'app_id' => $app_id,
        'api_url' => $api_url,
        'url' => $url,
        'method' => strtoupper($method),
        'body' => $haya_api_post_body,
    ));
}

/**
 * 记录日志
 * 
 * @author deatil
 * @create 2020-7-16
 */
function haya_api_post_read_by_tid_and_isfirst($tid, $isfirst) {
    $post = db_find_one('post', array(
        'tid' => $tid,
        'isfirst' => $isfirst,
    ));
    post_format($post);
    return $post;
}

/**
 * PHP判断当前协议是否为HTTPS
 * 
 * @author deatil
 * @create 2020-8-11
 */
function is_https() {
    if ( !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
        return true;
    } elseif ( isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
        return true;
    } elseif ( !empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
        return true;
    }
    return false;
}

?>