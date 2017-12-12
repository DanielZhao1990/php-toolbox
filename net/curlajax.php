<?php
namespace toolbox\net;
class curlajax {
	public $cookiejar;
	public $cookiefile;
	public $ua;
	public $debug;
	public $proxy;
	public $statusCode;

	function __construct() {
		$this->debug = 0;
		$this->ua = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.158888800.95 Safari/537.36 SE 2.X MetaSr 1.0';
		$this->cookiejar = 'cookie.txt';
		$this->cookiefile = 'cookie.txt';
	}

	/****************************
	 *get 请求资源
	 *@param string 地址
	 *@param string referer
	 *@param boolen 是否返回头部
	 *@param array 头部附加cookie
	 ****************************/
	function httpget($url, $referer = '', $withhead = 0) {
		$ch = curl_init();
		$r = $this->do_request($url,$referer, $withhead, $ch);
		return $r;
	}

	/****************************
	 *post 请求资源
	 *@param string 地址
	 *@param string referer
	 *@param array 提交数据
	 *@param boolen 是否返回头部
	 ****************************/
	function httppost($url, $referer = '', $postdata = array(), $withhead = 0) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
		$r = $this->do_request($url,$referer, $withhead, $ch);
		return $r;
	}

	/**
	 * @param $referer
	 * @param $withhead
	 * @param $ch
	 * @return mixed
	 */
	protected function do_request($url,$referer, $withhead, $ch)
	{
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		if ($this->proxy) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		if($withhead)
		{
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $withhead);
		}else{
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookiejar);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiefile);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->ua);
			curl_setopt($ch, CURLOPT_REFERER, $referer);
		}
		//curl_setopt($ch,CURLOPT_AUTOREFERER,1);
		$r = curl_exec($ch);
		$this->statusCode=curl_getinfo($ch,CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $r;
	}
}
