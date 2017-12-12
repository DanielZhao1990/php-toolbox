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
    public static function searchStrPos($needle,$str)
    {
        $posArr=array();
        $startIndex=0;
        while(($pos=strpos($str,$needle,$startIndex))!==false)
        {
            $posArr[]=$pos;
            $startIndex=$pos+1;
        }
        return $posArr;
    }

}