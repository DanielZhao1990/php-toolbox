<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/1/13
 * Time: 17:57
 */

namespace toolbox\net;


class UrlHelper
{
    public static function availableUrl($url)
    {
        // 避免请求超时超过了PHP的执行时间
        $executeTime = ini_get('max_execution_time');
        ini_set('max_execution_time', 0);
        $headers = @get_headers($url);
        ini_set('max_execution_time', $executeTime);
        if ($headers) {
            $head = explode(' ', $headers[0]);
            if (!empty($head[1]) && intval($head[1]) < 400)
                return true;
        }
    }


    public static function vueUrl()
    {

    }

    /**
     *  URL base64编码
     * '+' -> '-'
     * '/' -> '_'
     * '=' -> ''
     * @description
     * @author: daniel
     * @param $string
     * @return mixed|string
     */
    static function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     *
     * URL base64解码
     * '-' -> '+'
     * '_' -> '/'
     * 字符串长度%4的余数，补'='
     * @description
     * @author: daniel
     * @param $string
     * @return bool|string
     */
    static function urlsafe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * 返回当前请求的Url
     * @description
     * @author: daniel
     * @return string
     */
    public static function currentUrl()
    {
        $port = $_SERVER["SERVER_PORT"] == "80" ? "" : ":" . $_SERVER["SERVER_PORT"];
        return $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . "$port" . $_SERVER["REQUEST_URI"];
    }
}