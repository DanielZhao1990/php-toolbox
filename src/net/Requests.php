<?php

namespace toolbox\net;
// +----------------------------------------------------------------------
// | PHPSpider [ A PHP Framework For Crawler ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 https://doc.phpspider.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Seatle Yang <seatle@foxmail.com>
// +----------------------------------------------------------------------

//----------------------------------
// PHPSpider请求类文件
//----------------------------------

class Requests
{

    static $instances = [];

    public static function instance($key = "default")
    {
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new static();
        }
        return self::$instances[$key];
    }

    public function __construct()
    {
        $this->init();
    }

    /**
     * 版本号
     * @var string
     */
    const VERSION = '1.2.0';

    protected $ch = null;
    protected $timeout = 10;
    //public $request = array(
    //'headers' => array()
    //);
    public $input_encoding = null;
    public $output_encoding = null;
    public $cookies = array();
    public $domain_cookies = array();
    public $hosts = array();
    public $headers = array();
    public $useragents = array();
    public $client_ips = array();
    public $proxies = array();
    public $url = null;
    public $domain = null;
    public $raw = null;
    public $content = null;
    public $info = array();
    public $status_code = 0;
    public $error = null;

    /**
     * set timeout
     *
     * @param init $timeout
     * @return
     */
    public function set_timeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * 设置代理
     *
     * @param mixed $proxies
     * array (
     *    'http': 'socks5://user:pass@host:port',
     *    'https': 'socks5://user:pass@host:port'
     *)
     * @return void
     * @author seatle <seatle@foxmail.com>
     * @created time :2016-09-18 10:17
     */
    public function set_proxies($proxies)
    {
        $this->proxies = $proxies;
    }

    /**
     * 设置Headers
     *
     * @param string $headers
     * @return void
     */
    public function set_header($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * 设置COOKIE
     *
     * @param string $cookie
     * @return void
     */
    public function set_cookie($key, $value, $domain = '')
    {
        if (empty($key)) {
            return false;
        }
        if (!empty($domain)) {
            $this->domain_cookies[$domain][$key] = $value;
        } else {
            $this->cookies[$key] = $value;
        }
        return true;
    }

    public function set_cookies($cookies, $domain = '')
    {
        $cookies_arr = explode(";", $cookies);
        if (empty($cookies_arr)) {
            return false;
        }

        foreach ($cookies_arr as $cookie) {
            $cookie_arr = explode("=", $cookie);
            $key = $value = "";
            foreach ($cookie_arr as $k => $v) {
                if ($k == 0) {
                    $key = trim($v);
                } else {
                    $value .= trim(str_replace('"', '', $v));
                }
            }
            $key = strstr($cookie, '=', true);
            $value = substr(strstr($cookie, '='), 1);

            if (!empty($domain)) {
                $this->domain_cookies[$domain][$key] = $value;
            } else {
                $this->cookies[$key] = $value;
            }
        }
        return true;
    }

    public function get_cookie($name, $domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return '';
        }
        $cookies = empty($domain) ? $this->cookies : $this->domain_cookies[$domain];
        return isset($cookies[$name]) ? $cookies[$name] : '';
    }

    public function get_cookies($domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return array();
        }
        return empty($domain) ? $this->cookies : $this->domain_cookies[$domain];
    }

    public function del_cookies($domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return false;
        }
        if (empty($domain)) {
            $this->cookies = null;
        } else {
            unset($this->domain_cookies[$domain]);
        }
        return true;
    }


    /**
     * 设置随机的user_agent
     *
     * @param string $useragent
     * @return void
     */
    public function set_useragents($useragents)
    {
        $this->useragents = $useragents;
    }

    /**
     * 设置 user_agent
     *
     * @param string $useragent
     * @return void
     */
    public function set_useragent($useragent)
    {
        $this->headers['User-Agent'] = $useragent;
    }

    /**
     * set referer
     *
     */
    public function set_referer($referer)
    {
        $this->headers['Referer'] = $referer;
    }

    /**
     * 设置伪造IP
     *
     * @param string $ip
     * @return void
     */
    public function set_client_ip($ip)
    {
        $this->headers["CLIENT-IP"] = $ip;
        $this->headers["X-FORWARDED-FOR"] = $ip;
    }

    /**
     * 设置随机伪造IP
     *
     * @param mixed $ip
     * @return void
     * @author seatle <seatle@foxmail.com>
     * @created time :2016-11-16 11:06
     */
    public function set_client_ips($ips)
    {
        $this->client_ips = $ips;
    }

    /**
     * 设置Hosts
     *
     * @param string $hosts
     * @return void
     */
    public function set_hosts($host, $ips = array())
    {
        $ips = is_array($ips) ? $ips : array($ips);
        $this->hosts[$host] = $ips;
    }

    public function get_response_body($domain)
    {
        $header = $body = '';
        $http_headers = array();
        // 解析HTTP数据流
        if (!empty($this->raw)) {
            $this->get_response_cookies($domain);
            // body里面可能有 \r\n\r\n，但是第一个一定是HTTP Header，去掉后剩下的就是body
            $array = explode("\r\n\r\n", $this->raw);
            foreach ($array as $k => $v) {
                // post 方法会有两个http header：HTTP/1.1 100 Continue、HTTP/1.1 200 OK
                if (preg_match("#^HTTP/.*? 100 Continue#", $v)) {
                    unset($array[$k]);
                    continue;
                }
                if (preg_match("#^HTTP/.*? \d+ #", $v)) {
                    $header = $v;
                    unset($array[$k]);
                    $http_headers = $this->get_response_headers($v);
                }
            }
            $body = implode("\r\n\r\n", $array);
        }

        // 如果用户没有明确指定输入的页面编码格式(utf-8, gb2312)，通过程序去判断
        if ($this->input_encoding == null) {
            // 从头部获取
            preg_match("/charset=([^\s]*)/i", $header, $out);
            $encoding = empty($out[1]) ? '' : str_replace(array('"', '\''), '', strtolower(trim($out[1])));
            //$encoding = null;
            if (empty($encoding)) {
                // 在某些情况下,无法再 response header 中获取 html 的编码格式
                // 则需要根据 html 的文本格式获取
                $encoding = $this->get_encoding($body);
                $encoding = strtolower($encoding);
                if ($encoding == false || $encoding == "ascii") {
                    $encoding = 'gbk';
                }
            }
            $this->input_encoding = $encoding;
        }

        // 设置了输出编码的转码，注意: xpath只支持utf-8，iso-8859-1 不要转，他本身就是utf-8
        if ($this->output_encoding && $this->input_encoding != $this->output_encoding && $this->input_encoding != 'iso-8859-1') {
            // 先将非utf8编码,转化为utf8编码
            $body = @mb_convert_encoding($body, $this->output_encoding, $this->input_encoding);
            // 将页面中的指定的编码方式修改为utf8
            $body = preg_replace("/<meta([^>]*)charset=([^>]*)>/is", '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>', $body);
            // 直接干掉头部，国外很多信息是在头部的
            //$body = $this->_remove_head($body);
        }
        return $body;
    }

    public function get_response_cookies($domain)
    {
        // 解析Cookie并存入 $this->cookies 方便调用
        preg_match_all("/.*?Set\-Cookie: ([^\r\n]*)/i", $this->raw, $matches);
        $cookies = empty($matches[1]) ? array() : $matches[1];

        // 解析到Cookie
        if (!empty($cookies)) {
            $cookies = implode(";", $cookies);
            $cookies = explode(";", $cookies);
            foreach ($cookies as $cookie) {
                $cookie_arr = explode("=", $cookie);
                // 过滤 httponly、secure
                if (count($cookie_arr) < 2) {
                    continue;
                }
                $cookie_name = !empty($cookie_arr[0]) ? trim($cookie_arr[0]) : '';
                if (empty($cookie_name)) {
                    continue;
                }
                // 过滤掉domain路径
                if (in_array(strtolower($cookie_name), array('path', 'domain', 'expires', 'max-age'))) {
                    continue;
                }
                $this->domain_cookies[$domain][trim($cookie_arr[0])] = trim($cookie_arr[1]);
            }
        }
    }

    public function get_response_headers($html)
    {
        $header_lines = explode("\n", $html);
        if (!empty($header_lines)) {
            foreach ($header_lines as $line) {
                $header_arr = explode(":", $line);
                $key = empty($header_arr[0]) ? '' : trim($header_arr[0]);
                $val = empty($header_arr[1]) ? '' : trim($header_arr[1]);
                if (empty($key) || empty($val)) {
                    continue;
                }
                $headers[$key] = $val;
            }
        }
    }

    /**
     * 获取文件编码
     * @param $string
     * @return string
     */
    public function get_encoding($string)
    {
        $encoding = mb_detect_encoding($string, array('UTF-8', 'GBK', 'GB2312', 'LATIN1', 'ASCII', 'BIG5'));
        return strtolower($encoding);
    }

    /**
     * 移除页面head区域代码
     * @param $html
     * @return mixed
     */
    private function _remove_head($html)
    {
        return preg_replace('/<head.+?>.+<\/head>/is', '<head></head>', $html);
    }

    /**
     * 简单的判断一下参数是否为一个URL链接
     * @param  string $str
     * @return boolean
     */
    private function _is_url($url)
    {
        //$pattern = '/^http(s)?:\\/\\/.+/';
        $pattern = "/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/";
        if (preg_match($pattern, $url)) {
            return true;
        }
        return false;
    }

    /**
     * 初始化 CURL
     *
     */
    public function init()
    {
        if (!is_resource($this->ch)) {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
            curl_setopt($this->ch, CURLOPT_HEADER, false);
            curl_setopt($this->ch, CURLOPT_USERAGENT, "phpspider-requests/" . self::VERSION);
            curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout + 5);
            // 在多线程处理场景下使用超时选项时，会忽略signals对应的处理函数，但是无耐的是还有小概率的crash情况发生
            curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
        }
        return $this->ch;
    }

    /**
     * get
     *
     *
     */
    public function get($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'get', $fields);
    }

    /**
     * $fields 有三种类型:1、数组；2、http query；3、json
     * 1、array('name'=>'yangzetao') 2、http_build_query(array('name'=>'yangzetao')) 3、json_encode(array('name'=>'yangzetao'))
     * 前两种是普通的post，可以用$_POST方式获取
     * 第三种是post stream( json rpc，其实就是webservice )，虽然是post方式，但是只能用流方式 http://input 后者 $HTTP_RAW_POST_DATA 获取
     *
     * @param mixed $url
     * @param array $fields
     * @param mixed $proxies
     * @static
     * @access public
     * @return void
     */
    public function post($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'POST', $fields);
    }

    public function put($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'PUT', $fields);
    }

    public function delete($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'DELETE', $fields);
    }

    public function head($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'HEAD', $fields);
    }

    public function options($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'OPTIONS', $fields);
    }

    public function patch($url, $fields = array())
    {
        $this->init();
        return $this->request($url, 'PATCH', $fields);
    }

    public function request($url, $method = 'GET', $fields)
    {
        $method = strtoupper($method);
        if (!$this->_is_url($url)) {
            $this->error = "You have requested URL ({$url}) is not a valid HTTP address";
            return false;
        }

        // 如果是 get 方式，直接拼凑一个 url 出来
        if ($method == 'GET' && !empty($fields)) {
            $url = $url . (strpos($url, "?") === false ? "?" : "&") . http_build_query($fields);
        }

        $parse_url = parse_url($url);
        if (empty($parse_url) || empty($parse_url['host']) || !in_array($parse_url['scheme'], array('http', 'https'))) {
            $this->error = "No connection adapters were found for '{$url}'";
            return false;
        }
        $scheme = $parse_url['scheme'];
        $domain = $parse_url['host'];

        // 随机绑定 hosts，做负载均衡
        if ($this->hosts) {
            if (isset($this->hosts[$domain])) {
                $hosts = $this->hosts[$domain];
                $key = rand(0, count($hosts) - 1);
                $ip = $hosts[$key];
                $url = str_replace($domain, $ip, $url);
                $this->headers['Host'] = $domain;
            }
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);

        if ($method != 'GET') {
            // 如果是 post 方式
            if ($method == 'POST') {
                curl_setopt($this->ch, CURLOPT_POST, true);
            } else {
                $this->headers['X-HTTP-Method-Override'] = $method;
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
            }
            if (!empty($fields)) {
                if (is_array($fields)) {
                    $fields = http_build_query($fields);
                }
                // 不能直接传数组，不知道是什么Bug，会非常慢
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
                //curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $fields );
            }
        }

        $cookies = $this->get_cookies();
        $domain_cookies = $this->get_cookies($domain);
        $cookies = array_merge($cookies, $domain_cookies);
        // 是否设置了cookie
        if (!empty($cookies)) {
            foreach ($cookies as $key => $value) {
                $cookie_arr[] = $key . "=" . $value;
            }
            $cookies = implode("; ", $cookie_arr);
            curl_setopt($this->ch, CURLOPT_COOKIE, $cookies);
        }

        if (!empty($this->useragents)) {
            $key = rand(0, count($this->useragents) - 1);
            $this->headers['User-Agent'] = $this->useragents[$key];
        }

        if (!empty($this->client_ips)) {
            $key = rand(0, count($this->client_ips) - 1);
            $this->headers["CLIENT-IP"] = $this->client_ips[$key];
            $this->headers["X-FORWARDED-FOR"] = $this->client_ips[$key];
        }

        if ($this->headers) {
            $headers = array();
            foreach ($this->headers as $k => $v) {
                $headers[] = $k . ": " . $v;
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');

        // 关闭验证
        if ("https" == substr($url, 0, 5)) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if ($this->proxies) {
            if (!empty($this->proxies[$scheme])) {
                curl_setopt($this->ch, CURLOPT_PROXY, $this->proxies[$scheme]);
            }
        }

        // header + body，header 里面有 cookie
        curl_setopt($this->ch, CURLOPT_HEADER, true);

        $this->raw = curl_exec($this->ch);
        //var_dump($data);
        $this->info = curl_getinfo($this->ch);
        $this->status_code = $this->info['http_code'];
        if ($this->raw === false) {
            $this->error = ' Curl error: ' . curl_error($this->ch);
        }

        // 关闭句柄
        curl_close($this->ch);

        // 请求成功之后才把URL存起来
        $this->url = $url;
        $this->content = $this->get_response_body($domain);
        //$data = substr($data, 10);
        //$data = gzinflate($data);
        return $this->content;
    }

}


