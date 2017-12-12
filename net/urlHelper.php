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
}