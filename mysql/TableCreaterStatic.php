<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 16:58
 */

namespace toolbox\mysql;

class Gps
{
    static $tables = null;

    public static function checkAndCreateTable($mysql,$tableName)
    {
        if (self::$tables===null) {
            self::$tables = self::queryCurrentTables($mysql);
        }
        if (isset(self::$tables[$tableName])) {
            return true;
        } else {
            $true = self::createGpsTable($mysql, $tableName);
            self::$tables = self::queryCurrentTables($mysql);
            return isset(self::$tables[$tableName]);
        }
    }

    /**
     * @title queryCurrentTables
     * @description
     * @author: daniel
     * @param $mysql \toolbox\util\MysqlUtilIntf
     * @return array
     */
    public static function queryCurrentTables($mysql)
    {
        $tables = array();
        $sql = sprintf("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='%s' and TABLE_NAME like 'ceba_gps_%%';", "zsgj_gps");
        $tablesTemp = $mysql->execSql($sql);
        if ($tablesTemp) {
            foreach ($tablesTemp as $value) {
                $tables[$value["TABLE_NAME"]] = $value["TABLE_NAME"];
            }
        }
        return $tables;
    }
    /**
     * @title createGpsTable
     * @description
     * @author: daniel
     * @param $mysql \toolbox\util\MysqlUtilIntf
     * @param $tableName string
     */
    public static function createGpsTable($mysql, $tableName)
    {
        $sql = "CREATE TABLE`$tableName`(`bus_no`VARCHAR(20)DEFAULT NULL,`lat`DOUBLE DEFAULT NULL,`lng`DOUBLE DEFAULT NULL,`angle`INT(255)DEFAULT NULL,`time`datetime DEFAULT NULL,`speed`INT(11)DEFAULT NULL,`line_no`VARCHAR(10)DEFAULT NULL,`raw_data`text)ENGINE=ARCHIVE DEFAULT CHARSET=utf8;";
        return $mysql->query($sql);
    }
}