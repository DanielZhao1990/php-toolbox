<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-03-07
 * Time: 16:00
 */

namespace toolbox\util;


abstract class MysqlUtilIntf
{
    /**
     * @var
     */
    protected $db;
    protected $tableMap;
    protected $lastConnectTime;
    protected $connectTimeOut = 600;//10分钟

    public $dbName = "zsgjlog";
    protected $ip = "DatabaseServer";
    protected $port = 3306;
    protected $username = "root";
    protected $pass = "";
    protected $count = 0;
    protected $cacheList = array();//数据库插入队列. tableName -> list
    protected $cacheListCount = array();//数据库插入队列. tableName -> list
    protected $cacheListSize = 50;//数据库插入队列

    public function __construct($dbName, $ip = "DatabaseServer", $port = 3306, $username = "root", $pass = "")
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->username = $username;
        $this->pass = $pass;
        $this->dbName = $dbName;
        $this->tableMap = array();
        //定制结果集(表名things)
        $this->doConnect();
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
     * @param $data .应该是一个单数据.
     * @return bool
     */
    public function pushIntoCacheList($data, $tableName)
    {
        $count=1;
        if (is_array(current($data))) {
            foreach ($data as $item) {
                $this->cacheList[$tableName][] = $item;
            }
            $count=count($data);
        }else{
            $this->cacheList[$tableName][] = $data;
        }
        isset($this->cacheListCount[$tableName]) ? $this->cacheListCount[$tableName]+=$count : $this->cacheListCount[$tableName] = $count;
        if (max($this->cacheListCount) >= $this->cacheListSize) {
            foreach ($this->cacheList as $table => $values) {
                $this->execInsert($values, $table);
                unset($this->cacheList[$table]);
                unset($this->cacheListCount[$table]);
            }
        }
    }

    /**
     * 将缓存数据写入到数据库
     * @title flush
     * @description
     * @author: daniel
     */
    public function flush()
    {
        foreach ($this->cacheList as $table => $values) {
            $this->execInsert($values, $table);
            unset($this->cacheList[$table]);
            unset($this->cacheListCount[$table]);
        }
    }

    /**
     * 插入数据
     * @title execInsert
     * @description
     * @author: daniel
     * @param $data [] 可以是一维数据，也可以说二维
     * @param $tableName
     * @return bool
     * @throws \Exception
     */
    function execInsert($data, $tableName)
    {
        $this->checkConnectTimeOut();//检查连接是否过期
        $sql = "insert into " . $tableName;
        if ($data && is_array($data) && isset($data[0]) && is_array($data[0])) {
            $field = $this->parseKey($data[0]);
            $values = $this->parseArray($data, array_keys($data[0]));
        } else {
            $field = $this->parseKey($data);
            $values = $this->parseValue($data);
        }
        $sql = $sql . $field . " VALUES " . $values;
        $this->query($sql);
        return $this->db->errno == 0;
    }

    private function parseValue($data, $keys)
    {
        $str = "(";
        foreach ($keys as $key) {
            $isset=key_exists($key,$data);
            if ($isset) {
                $value = $data[$key];
                if (is_numeric($value)) {
                    $str .= $value . ",";
                } else if (is_string($value)){
                    $str .= "'" . $value . "',";
                }else if ($value===false||$value===null)
                {
                    $str .= "null,";
                }else{
                    throw new \Exception("数据类型异常");
                }
            }else{
                throw new \Exception("数据异常,$key 存在判断结果为".$isset.",数据:".print_r($data,true));
            }
        }
        return substr($str, 0, strlen($str) - 1) . ")";
    }

    private function parseArray($data, $keys)
    {
        $str = " ";
        foreach ($data as $value) {
            $str .= $this->parseValue($value, $keys) . ",";
        }
        return substr($str, 0, strlen($str) - 1);
    }

    private function parseKey($data)
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
    protected abstract function getError();
    
    /**
     * @return int
     */
    protected abstract function getErrno();

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    public abstract function query($sql);

    /**
     * @param $dbName
     * @param $ip
     * @param $port
     * @param $username
     * @param $pass
     */
    protected abstract function connect();

    public abstract function close();

    private function doConnect()
    {
        $this->lastConnectTime = time();
        $this->connect();
    }

    private function checkConnectTimeOut()
    {
        if (time() - $this->lastConnectTime >= $this->connectTimeOut) {
            $this->doConnect();
        }
    }


    public function select($sql)
    {
        $re=$this->query($sql);
        $result=false;
        if ($re)
        {
            $result=mysqli_fetch_array($re,MYSQL_ASSOC);
            mysql_free_result($re);
        }
        return $result;

    }
}