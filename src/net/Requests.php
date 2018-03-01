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

class Requests extends BaseRequests
{
    static $instances = [];

    /**
     * @description
     * @author: daniel
     * @param string $key
     * @return Requests
     */
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
     * 批量设置Header
     * @param array $headers
     * @return void
     */
    public function set_headers($headers)
    {
        foreach ($headers as $key => $value) {
            $this->set_header($key, $value);
        }
    }


    /**
     * 兼容V1.2.0版本,新版本只能使用旧版本的1个代理
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
        $this->set_proxy(array_pop($proxies));
    }
}