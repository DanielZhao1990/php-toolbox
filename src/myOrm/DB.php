<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2015/11/25
 * Time: 17:57
 */

namespace toolbox\myOrm;


use toolbox\myOrm\driver\MysqlDriver;

class DB {
    static $driver;
    const TYPE_MYSQL="mysql";
    const TYPE_SQLITE="sqlite";

    /**
     * 只调用一次.
     * @param $driver
     */
    public static function initial($type=DB::TYPE_MYSQL,$config=array())
    {
        defined("SHOW_SQL")? null:define("SHOW_SQL",true);
        if($type===DB::TYPE_MYSQL)
        {
            self::$driver=new MysqlDriver($config);
        }
    }

    public static function getDriver()
    {
        if(self::$driver)
        {
            return self::$driver;
        }else{
            exception("You should call intial driver first.");
        }
    }
}