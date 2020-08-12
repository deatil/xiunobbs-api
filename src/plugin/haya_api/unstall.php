<?php

/**
 * API
 * 
 * @author deatil
 * @create 2018-11-4
 */

defined('DEBUG') OR exit('Forbidden');

$tablepre = $db->tablepre;

db_exec("DROP TABLE IF EXISTS {$tablepre}haya_api_app;");
db_exec("DROP TABLE IF EXISTS {$tablepre}haya_api_log;");
db_exec("DROP TABLE IF EXISTS {$tablepre}haya_api_request_log;");
db_exec("DROP TABLE IF EXISTS {$tablepre}haya_api_session;");
db_exec("DROP TABLE IF EXISTS {$tablepre}haya_api_user_log;");

// 删除插件配置
kv_delete('haya_api'); 
kv_delete('haya_api_apis'); 

?>
