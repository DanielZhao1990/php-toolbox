<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/1/9
 * Time: 10:53
 */
namespace toolbox\common;
class TimeUtil
{
    public static function u2str($unixTime,$zero_msg="")
    {
        if(!$unixTime||$unixTime==0)
        {
            return $zero_msg;
        }
        return date("Y-m-d H:i:s",$unixTime);
    }
}