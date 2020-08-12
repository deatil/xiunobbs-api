<?php

!defined('DEBUG') and exit('Access Denied.');

/**
 * 获取API文件列表
 *
 * @create 2018-11-11
 * @author deatil
 */
function haya_api_apis_find_by_dir($api_dir = null) {
	if (empty($api_dir)) {
		return array();
	}
	
	if (!is_dir($api_dir)) {
		return array();
	}
	
	$api_files = glob($api_dir.'*.*');
	
	$files = array();
	if (!empty($api_files)) {
		foreach ($api_files as $file) {
			$api_file_data = haya_api_apis_get_file_data($file);
			$filename = basename($file);
			$filename_pathinfo = pathinfo($filename);
			$default_url = url('haya_api-'.$filename_pathinfo['filename']);
			$files[] = array(
				'id' => base64_encode($filename), 
				'md5' => md5_file($file), 
				'filename' => $filename, 
				'file' => $file, 
				'path' => dirname($file), 
				'filesize' => filesize($file), 
				'create_date' => filemtime($file), 
				
				'name' => !empty($api_file_data['Name']) ? $api_file_data['Name'] : $filename, 
				'url' => !empty($api_file_data['Url']) ? $api_file_data['Url'] : $default_url, 
				'method' => !empty($api_file_data['Method']) ? $api_file_data['Method'] : $filename, 
				'body' => !empty($api_file_data['Body']) ? $api_file_data['Body'] : $filename, 
				'response' => !empty($api_file_data['Response']) ? $api_file_data['Response'] : $filename, 
				'author' => !empty($api_file_data['Author']) ? $api_file_data['Author'] : '', 
				'keywords' => !empty($api_file_data['Keywords']) ? $api_file_data['Keywords'] : '', 
				'description' => !empty($api_file_data['Description']) ? $api_file_data['Description'] : '', 
			);
		}
	}
	
	return $files;
}

/**
 * 获取API文件
 *
 * @create 2018-11-11
 * @author deatil
 */
function haya_api_apis_read($api_file = null) {
	if (empty($api_file)) {
		return array();
	}
	
	if (!is_file($api_file)) {
		return array();
	}
	
	$file = $api_file;
	$api_file_data = haya_api_apis_get_file_data($api_file);
	
	$filename = basename($file);
	$filename_pathinfo = pathinfo($filename);
	$default_url = url('haya_api-'.$filename_pathinfo['filename']);
	$file_info = array(
		'id' => base64_encode($filename), 
		'md5' => md5_file($file), 
		'filename' => $filename, 
		'file' => $file, 
		'path' => dirname($file), 
		'filesize' => filesize($file), 
		'create_date' => filemtime($file), 
		
		'name' => !empty($api_file_data['Name']) ? $api_file_data['Name'] : $filename, 
		'url' => !empty($api_file_data['Url']) ? $api_file_data['Url'] : $default_url, 
		'method' => !empty($api_file_data['Method']) ? $api_file_data['Method'] : $filename, 
		'body' => !empty($api_file_data['Body']) ? $api_file_data['Body'] : $filename, 
		'response' => !empty($api_file_data['Response']) ? $api_file_data['Response'] : $filename, 
		'author' => !empty($api_file_data['Author']) ? $api_file_data['Author'] : '', 
		'keywords' => !empty($api_file_data['Keywords']) ? $api_file_data['Keywords'] : '', 
		'description' => !empty($api_file_data['Description']) ? $api_file_data['Description'] : '', 
	);
	
	return $file_info;
}

function haya_api_apis_get_file_data($file) {
	$fp = fopen( $file, 'r' );
	
	$file_data = fread( $fp, 8192 );

	fclose( $fp );

	$file_data = str_replace( "\r", "\n", $file_data );

	$all_headers = array(
		'Name'      => 'Name',
		'Url'    	=> 'Url',
		'Method' 	=> 'Method',
		'Body'      => 'Body',
		'Response'  => 'Response',
		'Code'  	=> 'Code',
		'Author'  	=> 'Author',
		'Keywords'  => 'Keywords',
		'Description' => 'Description',
	);

	foreach ( $all_headers as $field => $regex ) {
		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, $match ) && $match[1] ) {
			$all_headers[ $field ] = haya_api_apis_cleanup_header_comment( $match[1] );
		} else {
			$all_headers[ $field ] = '';
		}
	}

	return $all_headers;
}

function haya_api_apis_cleanup_header_comment( $str ) {
	return trim(preg_replace("/\s*(?:\*\/|\?>).*/", '', $str));
}

?>