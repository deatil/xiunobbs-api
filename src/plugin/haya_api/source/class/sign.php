<?php

defined('DEBUG') OR exit('Forbidden');

/**
 * 签名
 *
 * @create 2018-11-4
 * @author deatil
 */
class haya_api_sign 
{

	/**
	 * 验证签名
	 * @param $data
	 * @param $key
	 * @return string
	 *
	 * @create 2018-11-4
	 * @author deatil
	 */
	public function check_sign(
		$data = array(), 
		$key = ''
	) {
		if (!isset($data['sign'])) {
			return false;
		}
		
		$sign = $data['sign'];
		$make_sign = $this->get_sign($data, $key);
		
		if ($sign != $make_sign) {
			return false;
		}

		return true;
	}

	/**
	 * 生成签名
	 * @param $data
	 * @param $key
	 * @return string
	 *
	 * @create 2018-11-4
	 * @author deatil
	 */
	public function get_sign($data = array(), $key = '') 
	{
		ksort($data);
		$string = md5($this->get_sign_content($data) . '&key=' . $key);
		return strtoupper($string);
	}

	/**
	 * 生成签名内容
	 * @param $data
	 * @return string
	 *
	 * @create 2018-11-4
	 * @author deatil
	 */
	public function get_sign_content($data)
	{		
		$buff = '';
		foreach ($data as $k => $v) {
			$buff .= ($k != 'sign' && $v != '' && !is_array($v)) ? $k . '=' . $v . '&' : '';
		}
		return trim($buff, '&');
	}

	/**
	 * 生成随机字符串
	 * @param int $length
	 * @return string
	 *
	 * @create 2018-11-4
	 * @author deatil
	 */
	public function create_nonce_str($length = 16)
	{
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	
}
	
?>