<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 11:20
 */

namespace toolbox\util;


class BaseCounter
{
    
    public static function printLog($data)
    {
        echo $data."\n";
    }


    public static function printTestLog($data)
    {
        $filename = dirname(APP_PATH) . '/log/consumptionTime.txt';
        $data = print_r($data, true)."\n";
        file_put_contents($filename,$data,FILE_APPEND);
    }
}