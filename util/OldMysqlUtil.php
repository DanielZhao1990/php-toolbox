<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-02-23
 * Time: 14:20
 */

namespace toolbox\util;

    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */
/**
 * Description of DMysql
 *
 * @author daniel
 */
class OldMysqlUtil
{
    private $db;
    private $tableMap;

    private $dbName = "zsgjlog";
    private $ip = "DatabaseServer";
    private $port = 3306;
    private $username = "root";
    private $pass = "";
    private $count = 0;
    private $cacheList = array();//数据库插入队列. tableName -> list
    private $cacheListCount = array();//数据库插入队列. tableName -> list
    private $cacheListSize = 50;//数据库插入队列


//    private before
    public function __construct($dbName, $ip = "DatabaseServer", $port = 3306, $username = "root", $pass = "")
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->username = $username;
        $this->pass = $pass;
        $this->dbName = $dbName;
        $this->tableMap = array();
        //定制结果集(表名things)
    }


    /**
     * @param int $cacheListSize
     */
    public function setCacheListSize($cacheListSize)
    {
        $this->cacheListSize = $cacheListSize;
    }

    /**
     * @return string
     */
    public function getDbName()
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }


    public function insert($data, $tableName)
    {
        return $this->execInsert($data, $tableName);
    }

    /**
     *
     * @param $tableName
     * @param $data 应该是一个单数据.
     * @return bool
     */
    public function pushIntoCacheList($data, $tableName)
    {
        $this->cacheList[$tableName][] = $data;
        isset($this->cacheListCount[$tableName]) ? $this->cacheListCount[$tableName]++ : $this->cacheListCount[$tableName] = 1;
        if (max($this->cacheListCount) >= $this->cacheListSize) {
            foreach ($this->cacheList as $table => $values) {
                $this->execInsert($values, $table);
                unset($this->cacheList[$table]);
                unset($this->cacheListCount[$table]);
            }
        }
    }

    function execInsert($data, $tableName)
    {
        $this->connect();
        $sql = "insert into " . $tableName;
        if ($data && is_array($data) && isset($data[0]) && is_array($data[0])) {
            $field = $this->parseKey($data[0]);
            $values = $this->parseArray($data);
        } else {
            $field = $this->parseKey($data);
            $values = $this->parseValue($data);
        }
        $sql = $sql . $field . " VALUES " . $values;
        $result = $this->query($sql);
        if ($this->getErrno()) {
            L($this->getError());
        }
        $this->count++;
        $this->close();
        return $result;
    }

    function parseValue($data)
    {
        $str = "(";
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $str .= $value . ",";
            } else {
                $str .= "'" . $value . "',";
            }
        }
        return substr($str, 0, strlen($str) - 1) . ")";
    }

    function parseArray($data)
    {
        $str = " ";
        foreach ($data as $value) {
            $str .= $this->parseValue($value) . ",";
        }
        return substr($str, 0, strlen($str) - 1);
        return $str;
    }

    function parseKey($data)
    {
        $str = "(";
        foreach ($data as $key => $value) {
            $str .= "`" . $key . "`,";
        }
        return substr($str, 0, strlen($str) - 1) . ")";
    }

    /**
     * @return string
     */
    protected function getError()
    {
        return mysql_error();
    }

    /**
     * @return int
     */
    protected function getErrno()
    {
        return mysql_errno();
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    protected function query($sql)
    {
        return mysql_query($sql);
    }

    /**
     * @param $dbName
     * @param $ip
     * @param $port
     * @param $username
     * @param $pass
     */
    protected function connect()
    {
        $this->db = mysql_connect($this->ip . ":" . $this->port, $this->username, $this->pass);
        if ($this->getError()) {
            die('Connect Error (' . $this->getErrno() . ') '
                . $this->getError());
        }
        $this->query("set names 'utf8';");
        mysql_select_db($this->dbName);
    }

    private function close()
    {
        mysql_close($this->db);
    }
}
