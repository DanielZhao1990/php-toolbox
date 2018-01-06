<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/13
 * Time: 10:02
 */

namespace toolbox\util;


class StringUtil
{
    public static function searchStrPos($needle, $str)
    {
        $posArr = array();
        $startIndex = 0;
        while (($pos = strpos($str, $needle, $startIndex)) !== false) {
            $posArr[] = $pos;
            $startIndex = $pos + 1;
        }
        return $posArr;
    }

    /*
     * 下划线转驼峰
     */
    public static function underline2Hump($str)
    {
        $str = preg_replace_callback('/([-_]+([a-z]{1}))/i', function ($matches) {
            return strtoupper($matches[2]);
        }, $str);
        return $str;
    }

    /*
     * 驼峰转下划线
     */
    public static function hump2Underline($str)
    {
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '_' . strtolower($matches[0]);
        }, $str);
        if (strpos($str, "_") === 0) {
            return substr($str, 1);
        }
        return $str;
    }

}