<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-03-07
 * Time: 16:00
 */

namespace toolbox\util;


class MysqlPDOUtil extends MysqlUtilIntf
{

    /**
     * @return string
     */
    protected function getError()
    {
        $this->db->errorInfo();
    }

    /**
     * @return int
     */
    protected function getErrno()
    {
        $this->db->errorCode();
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    protected function query($sql)
    {
        $this->db->exec($sql);
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
        $dsn = "mysql:host=$this->ip;dbname=$this->dbName";
        $this->db = new \PDO($dsn, $this->username, $this->pass,array(\PDO::ATTR_PERSISTENT => true));
    }

    public function close()
    {
        $this->db=null;
        unset($this->db);
    }
}