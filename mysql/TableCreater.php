<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 16:58
 */

namespace toolbox\mysql;

class TableCreater
{
    static $tables = null;
    private $tablePre = "";
    private $dbName="";
    /**
     * @var \toolbox\util\MysqlUtilIntf
     */
    private $mysql;
//    protected $createSQL="CREATE TABLE `%s`(`bus_no`VARCHAR(20)DEFAULT NULL,`lat`DOUBLE DEFAULT NULL,`lng`DOUBLE DEFAULT NULL,`angle`INT(255)DEFAULT NULL,`time`datetime DEFAULT NULL,`speed`INT(11)DEFAULT NULL,`line_no`VARCHAR(10)DEFAULT NULL,`raw_data`text)ENGINE=ARCHIVE DEFAULT CHARSET=utf8;";
    protected $createSQL="CREATE TABLE`%s`(`uid`int(11)DEFAULT NULL,`time`date DEFAULT NULL,`exe_time`double DEFAULT NULL,`controller`varchar(255)DEFAULT NULL,`action`varchar(255)DEFAULT NULL,`ip`varchar(40)DEFAULT NULL)ENGINE=ARCHIVE DEFAULT CHARSET=utf8";

    /**
     * TableCreater constructor.
     * @param string $tablePre
     * @param string $dbName
     * @param $mysql \toolbox\util\MysqlUtilIntf
     */
    public function __construct($tablePre, $mysql)
    {
        $this->tablePre = $tablePre;
        $this->mysql = $mysql;
        $this->dbName = $mysql->dbName;
    }


    public function checkAndCreateTable($tableName)
    {
        if (is_int($tableName))
        {
            $tableName=$this->generateTableName($tableName);
        }
        if (self::$tables === null) {
            self::$tables = self::queryCurrentTables();
        }
        if (isset(self::$tables[$tableName])) {
            return true;
        } else {
            $this->createGpsTable($tableName);
            self::$tables = self::queryCurrentTables();
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
    public function queryCurrentTables()
    {
        $tables = array();
        $sql = sprintf("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA='%s' and TABLE_NAME like '{$this->tablePre}_%%';",$this->dbName);
        $tablesTemp = $this->mysql->execSql($sql);
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
    public function createGpsTable($tableName)
    {
        $sql = sprintf($this->createSQL,$tableName);
        return $this->mysql->query($sql);
    }

    public function generateTableName($time)
    {
        return $this->tablePre."_".date("Ymd",$time);
    }
}