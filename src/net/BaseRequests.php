<?php
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
namespace toolbox\net;

if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

class BaseRequests
{
    const VERSION = '2.0.0';

    protected $ch = null;

    /**** Public variables ****/

    /* user definable vars */

    public $timeout = 5;
    public $encoding = null;
    public $input_encoding = null;
    public $output_encoding = null;
    public $cookies = array();                           // array of cookies to pass
    // $cookies['username'] = "seatle";
    public $rawheaders = array();                        // array of raw headers to send
    public $domain_cookies = array();                    // array of cookies for domain to pass
    public $hosts = array();                             // random host binding for make request faster
    public $headers = array();                           // headers returned from server sent here
    public $useragents = array("requests/2.0.0");        // random agent we masquerade as
    public $client_ips = array();                        // random ip we masquerade as
    public $proxies = array();                           // random proxy ip
    public $raw = "";                                    // head + body content returned from server sent here
    public $head = "";                                   // head content
    public $content = "";                                // The body before encoding
    public $text = "";                                   // The body after encoding
    public $info = array();                              // curl info
    public $history = 302;                               // http request status before redirect. ex:30x
    public $status_code = 0;                             // http request status
    public $error = "";                                  // error messages sent here

    /**
     * set timeout
     * $timeout 为数组时会分别设置connect和read
     * @param init or array $timeout
     * @return
     */
    public function set_timeout($timeout)
    {
        $this->timeout = $timeout;
        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
    }

    /**
     * 设置代理
     * 如果代理有多个，请求时会随机使用
     *
     * @param mixed $proxies
     * array (
     *    'socks5://user1:pass2@host:port',
     *    'socks5://user2:pass2@host:port'
     *)
     * @return mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2016-09-18 10:17
     */
    public function set_proxy($proxy)
    {
        $this->proxies = is_array($proxy) ? $proxy : array($proxy);
    }

    /**
     * 自定义请求头部
     * 请求头内容可以用 requests::$rawheaders 来获取
     * 比如获取Content-Type：requests::$rawheaders['Content-Type']
     *
     * @param string $headers
     * @return mixed
     */
    public function set_header($key, $value)
    {
        $this->rawheaders[$key] = $value;
    }

    /**
     * 设置全局COOKIE
     *
     * @param string $cookie
     * @return mixed
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

    /**
     * 批量设置全局cookie
     *
     * @param mixed $cookies
     * @param string $domain
     * @return mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function set_cookies($cookies, $domain = '')
    {
        $cookies_arr = explode(";", $cookies);
        if (empty($cookies_arr)) {
            return false;
        }

        foreach ($cookies_arr as $cookie) {
            $cookie_arr = explode("=", $cookie, 2);
            $key = $cookie_arr[0];
            $value = empty($cookie_arr[1]) ? '' : $cookie_arr[1];

            if (!empty($domain)) {
                $this->domain_cookies[$domain][$key] = $value;
            } else {
                $this->cookies[$key] = $value;
            }
        }
        return true;
    }

    /**
     * 获取单一Cookie
     *
     * @param mixed $name cookie名称
     * @param string $domain 不传则取全局cookie，就是手动set_cookie的cookie
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function get_cookie($name, $domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return '';
        }
        $cookies = empty($domain) ? $this->cookies : $this->domain_cookies[$domain];
        return isset($cookies[$name]) ? $cookies[$name] : '';
    }

    /**
     * 获取Cookie数组
     *
     * @param string $domain 不传则取全局cookie，就是手动set_cookie的cookie
     * @return mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function get_cookies($domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return array();
        }
        return empty($domain) ? $this->cookies : $this->domain_cookies[$domain];
    }

    /**
     * 删除Cookie
     *
     * @param string $domain 不传则删除全局Cookie
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function del_cookie($key, $domain = '')
    {
        if (empty($key)) {
            return false;
        }

        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return false;
        }

        if (!empty($domain)) {
            if (isset($this->domain_cookies[$domain][$key])) {
                unset($this->domain_cookies[$domain][$key]);
            }
        } else {
            if (isset($this->cookies[$key])) {
                unset($this->cookies[$key]);
            }
        }
        return true;
    }

    /**
     * 删除Cookie
     *
     * @param string $domain 不传则删除全局Cookie
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function del_cookies($domain = '')
    {
        if (!empty($domain) && !isset($this->domain_cookies[$domain])) {
            return false;
        }
        if (empty($domain)) {
            $this->cookies = array();
        } else {
            if (isset($this->domain_cookies[$domain])) {
                unset($this->domain_cookies[$domain]);
            }
        }
        return true;
    }

    /**
     * 设置随机的user_agent
     *
     * @param string $useragent
     * mixed
     */
    public function set_useragent($useragent)
    {
        $this->useragents = is_array($useragent) ? $useragent : array($useragent);
    }

    /**
     * set referer
     *
     */
    public function set_referer($referer)
    {
        $this->rawheaders['Referer'] = $referer;
    }

    /**
     * 设置伪造IP
     * 传入数组则为随机IP
     * @param string $ip
     * mixed
     */
    public function set_client_ip($ip)
    {
        $this->client_ips = is_array($ip) ? $ip : array($ip);
    }

    /**
     * 设置Hosts
     * 负载均衡到不同的服务器，如果对方使用CDN，采用这个是最好的了
     *
     * @param string $hosts
     * mixed
     */
    public function set_hosts($host, $ips = array())
    {
        $ips = is_array($ips) ? $ips : array($ips);
        $this->hosts[$host] = $ips;
    }

    /**
     * 分割返回的header和body
     * header用来判断编码和获取Cookie
     * body用来判断编码，得到编码前和编码后的内容
     *
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function split_header_body()
    {
        $head = $body = '';
        $head = substr($this->raw, 0, $this->info['header_size']);
        $body = substr($this->raw, $this->info['header_size']);
        // http header
        $this->head = $head;
        // The body before encoding
        $this->content = $body;

        //$http_headers = array();
        //// 解析HTTP数据流
        //if (!empty($this->raw))
        //{
        //$this->get_response_cookies($domain);
        //// body里面可能有 \r\n\r\n，但是第一个一定是HTTP Header，去掉后剩下的就是body
        //$array = explode("\r\n\r\n", $this->raw);
        //foreach ($array as $k=>$v)
        //{
        //// post 方法会有两个http header：HTTP/1.1 100 Continue、HTTP/1.1 200 OK
        //if (preg_match("#^HTTP/.*? 100 Continue#", $v))
        //{
        //unset($array[$k]);
        //continue;
        //}
        //if (preg_match("#^HTTP/.*? \d+ #", $v))
        //{
        //$header = $v;
        //unset($array[$k]);
        //$http_headers = $this->get_response_headers($v);
        //}
        //}
        //$body = implode("\r\n\r\n", $array);
        //}

        // 如果用户没有明确指定输入的页面编码格式(utf-8, gb2312)，通过程序去判断
        if ($this->input_encoding == null) {
            // 从头部获取
            preg_match("/charset=([^\s]*)/i", $head, $out);
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

            // 没有转码前
            $this->encoding = $encoding;
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

            // 转码后
            $this->encoding = $this->output_encoding;
        }

        // The body after encoding
        $this->text = $body;
        return array($head, $body);
    }

    /**
     * 获得域名相对应的Cookie
     *
     * @param mixed $header
     * @param mixed $domain
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function get_response_cookies($header, $domain)
    {
        // 解析Cookie并存入 $this->cookies 方便调用
        preg_match_all("/.*?Set\-Cookie: ([^\r\n]*)/i", $header, $matches);
        $cookies = empty($matches[1]) ? array() : $matches[1];

        // 解析到Cookie
        if (!empty($cookies)) {
            $cookies = implode(";", $cookies);
            $cookies = explode(";", $cookies);
            foreach ($cookies as $cookie) {
                $cookie_arr = explode("=", $cookie, 2);
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

    /**
     * 获得response header
     * 此方法占时没有用到
     *
     * @param mixed $header
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function get_response_headers($header)
    {
        $headers = array();
        $header_lines = explode("\n", $header);
        if (!empty($header_lines)) {
            foreach ($header_lines as $line) {
                $header_arr = explode(":", $line, 2);
                $key = empty($header_arr[0]) ? '' : trim($header_arr[0]);
                $val = empty($header_arr[1]) ? '' : trim($header_arr[1]);
                if (empty($key) || empty($val)) {
                    continue;
                }
                $headers[$key] = $val;
            }
        }
        $this->headers = $headers;
        return $this->headers;
    }

    /**
     * 获取编码
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
    private static function _remove_head($html)
    {
        return preg_replace('/<head.+?>.+<\/head>/is', '<head></head>', $html);
    }

    /**
     * 简单的判断一下参数是否为一个URL链接
     * @param  string $str
     * @return boolean
     */
    private static function _is_url($url)
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
            curl_setopt($this->ch, CURLOPT_HEADER, false);
            curl_setopt($this->ch, CURLOPT_USERAGENT, "phpspider-requests/" . self::VERSION);
            // 如果设置了两个时间，就分开设置
            if (is_array($this->timeout)) {
                curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout[0]);
                curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout[1]);
            } else {
                curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
                curl_setopt($this->ch, CURLOPT_TIMEOUT, $this->timeout);
            }
            // 在多线程处理场景下使用超时选项时，会忽略signals对应的处理函数，但是无耐的是还有小概率的crash情况发生
            curl_setopt($this->ch, CURLOPT_NOSIGNAL, true);
        }
        return $this->ch;
    }

    /**
     * get 请求
     */
    public function get($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'get', $fields, NULL, $allow_redirects, $cert);
    }

    /**
     * post 请求
     * $fields 有三种类型:1、数组；2、http query；3、json
     * 1、array('name'=>'yangzetao')
     * 2、http_build_query(array('name'=>'yangzetao'))
     * 3、json_encode(array('name'=>'yangzetao'))
     * 前两种是普通的post，可以用$_POST方式获取
     * 第三种是post stream( json rpc，其实就是webservice )
     * 虽然是post方式，但是只能用流方式 http://input 后者 $HTTP_RAW_POST_DATA 获取
     *
     * @param mixed $url
     * @param array $fields
     * @param mixed $proxies
     * @static
     * @access public
     * mixed
     */
    public function post($url, $fields = array(), $files = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'POST', $fields, $files, $allow_redirects, $cert);
    }

    public function put($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'PUT', $fields, $allow_redirects, $cert);
    }

    public function delete($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'DELETE', $fields, $allow_redirects, $cert);
    }

    // 响应HTTP头域里的元信息
    // 此方法被用来获取请求实体的元信息而不需要传输实体主体（entity-body）
    // 此方法经常被用来测试超文本链接的有效性，可访问性，和最近的改变。.
    public function head($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        $this->request($url, 'HEAD', $fields, $allow_redirects, $cert);
    }

    public function options($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'OPTIONS', $fields, $allow_redirects, $cert);
    }

    public function patch($url, $fields = array(), $allow_redirects = true, $cert = NULL)
    {
        $this->init();
        return $this->request($url, 'PATCH', $fields, $allow_redirects, $cert);
    }

    /**
     * request
     *
     * @param mixed $url 请求URL
     * @param string $method 请求方法
     * @param array $fields 表单字段
     * @param array $files 上传文件
     * @param mixed $cert CA证书
     * mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function request($url, $method = 'GET', $fields = array(), $files = array(), $allow_redirects = true, $cert = NULL)
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
                $this->rawheaders['Host'] = $domain;
            }
        }

        curl_setopt($this->ch, CURLOPT_URL, $url);

        if ($method != 'GET') {
            // 如果是 post 方式
            if ($method == 'POST') {
                curl_setopt($this->ch, CURLOPT_POST, true);
                $file_fields = array();
                if (!empty($files)) {
                    foreach ($files as $postname => $file) {
                        $filepath = realpath($file);
                        // 如果文件不存在
                        if (!file_exists($filepath)) {
                            continue;
                        }

                        $filename = basename($filepath);
                        $type = $this->get_mimetype($filepath);
                        $file_fields[$postname] = curl_file_create($filepath, $type, $filename);
                        // curl -F "name=seatle&file=@/absolute/path/to/image.png" htt://localhost/uploadfile.php
                        //$cfile = '@'.realpath($filename).";type=".$type.";filename=".$filename;
                    }
                }
            } else {
                $this->rawheaders['X-HTTP-Method-Override'] = $method;
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
            }
            if (!empty($fields)) {
                // 不是上传文件的，用http_build_query, 能实现更好的兼容性，更小的请求数据包
                if (empty($file_fields)) {
                    // post方式
                    if (is_array($fields)) {
                        $fields = http_build_query($fields);
                    }
                } else {
                    // 有post数据
                    if (is_array($fields) && !empty($fields)) {
                        // 某些server可能会有问题
                        $fields = array_merge($fields, $file_fields);
                    } else {
                        $fields = $file_fields;
                    }
                }
                // 不能直接传数组，不知道是什么Bug，会非常慢
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
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
            $this->rawheaders['User-Agent'] = $this->useragents[$key];
        }

        if (!empty($this->client_ips)) {
            $key = rand(0, count($this->client_ips) - 1);
            $this->rawheaders["CLIENT-IP"] = $this->client_ips[$key];
            $this->rawheaders["X-FORWARDED-FOR"] = $this->client_ips[$key];
        }

        if ($this->rawheaders) {
            $headers = array();
            foreach ($this->rawheaders as $k => $v) {
                $headers[] = $k . ": " . $v;
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($this->ch, CURLOPT_ENCODING, 'gzip');

        // 关闭验证
        if ($scheme == 'https') {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        if ($this->proxies) {
            $key = rand(0, count($this->proxies) - 1);
            $proxy = $this->proxies[$key];
            curl_setopt($this->ch, CURLOPT_PROXY, $proxy);
        }

        // header + body，header 里面有 cookie
        curl_setopt($this->ch, CURLOPT_HEADER, true);
        // 请求跳转后的内容
        if ($allow_redirects) {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        }

        $this->raw = curl_exec($this->ch);
        // 真实url
        //$location = curl_getinfo( $this->ch, CURLINFO_EFFECTIVE_URL);
        $this->info = curl_getinfo($this->ch);
        //print_r($this->info);
        $this->status_code = $this->info['http_code'];
        if ($this->raw === false) {
            $this->error = 'Curl error: ' . curl_error($this->ch);
            //trigger_error($this->error, E_USER_WARNING);
        }

        // 关闭句柄
        curl_close($this->ch);

        // 请求成功之后才把URL存起来
        list($header, $text) = $this->split_header_body();
        $this->history = $this->get_history($header);
        $this->headers = $this->get_response_headers($header);
        $this->get_response_cookies($header, $domain);
        //$data = substr($data, 10);
        //$data = gzinflate($data);
        return $text;
    }

    public function get_history($header)
    {
        $status_code = 0;
        $lines = explode("\n", $header);
        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match("#^HTTP/.*? (\d+) Found#", $line, $out)) {
                $status_code = empty($out[1]) ? 0 : intval($out[1]);
            }
        }
        return $status_code;
    }

    // 获取 mimetype
    public function get_mimetype($filepath)
    {
        $fp = finfo_open(FILEINFO_MIME);
        $mime = finfo_file($fp, $filepath);
        finfo_close($fp);
        $arr = explode(";", $mime);
        $type = empty($arr[0]) ? '' : $arr[0];
        return $type;
    }

    /**
     * 拼凑文件和表单
     * 占时没有用到
     *
     * @param mixed $post_fields
     * @param mixed $file_fields
     * @return mixed
     * @author seatle <seatle@foxmail.com>
     * @created time :2017-08-03 18:06
     */
    public function get_postfile_form($post_fields, $file_fields)
    {
        // 构造post数据
        $data = '';
        $delimiter = '-------------' . uniqid();
        // 表单数据
        foreach ($post_fields as $name => $content) {
            $data .= "--" . $delimiter . "\r\n";
            $data .= 'Content-Disposition: form-data; name = "' . $name . '"';
            $data .= "\r\n\r\n";
            $data .= $content;
            $data .= "\r\n";
        }

        foreach ($file_fields as $input_name => $file) {
            $data .= "--" . $delimiter . "\r\n";
            $data .= 'Content-Disposition: form-data; name = "' . $input_name . '";' .
                ' filename="' . $file['filename'] . '"' . "\r\n";
            $data .= "Content-Type: {$file['type']}\r\n";
            $data .= "\r\n";
            $data .= $file['content'];
            $data .= "\r\n";
        }

        // 结束符
        $data .= "--" . $delimiter . "--\r\n";

        //return array(
        //CURLOPT_HTTPHEADER => array(
        //'Content-Type:multipart/form-data;boundary=' . $delimiter,
        //'Content-Length:' . strlen($data)
        //),
        //CURLOPT_POST => true,
        //CURLOPT_POSTFIELDS => $data,
        //);
        return array($delimiter, $data);
    }
}

