<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/28
 * Time: 11:40
 */

namespace toolbox\util;


class LinuxProcessUtil
{
    public static function killByName($name)
    {
        if (SystemUtil::isLinux())
        {
            $exec ="sudo ps -aux|grep $name|grep -v grep|cut -c 9-15|sudo xargs kill -9";
            exec($exec);
        }
       
    }
}