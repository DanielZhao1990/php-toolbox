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
use mysqli;

/**
 * Description of DMysql
 *
 * @author daniel
 */
class MysqlUtil extends MysqlUtilIntf
{


    public function execSql($sql)
    {
        $result=$this->query($sql);
        if($result->num_rows>0)
        {
            while ($row = $result->fetch_assoc()) {
                $results_array[] = $row;
            }
            return $results_array;
        }
        return false;
    }

    /**
     * @return string
     */
    protected function getError()
    {
        return $this->db->error;
    }

    /**
     * @return int
     */
    protected function getErrno()
    {
        return $this->db->errno;
    }

    /**
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query($sql)
    {
        $result=$this->db->query($sql);
        if ($this->db->errno) {
            if ($this->db->errno==2006)
            {
                $this->close();
                $this->connect();
            }
            L($this->db->errno);
            L($this->db->error);
        }
        return $result;
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
        $this->db = new mysqli($this->ip, $this->username, $this->pass, $this->dbName, $this->port);
        if ($this->db->connect_error) {
            die('Connect Error (' . $this->db->connect_errno . ') '
                . $this->db->connect_error);
        }
        $this->db->query("set names 'utf8'");

    }

    public function close()
    {
        $this->db->close();
    }
}
