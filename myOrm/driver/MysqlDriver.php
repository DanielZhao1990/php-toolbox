<?php
namespace toolbox\myOrm\driver;

class MysqlDriver extends BaseDriver
{
    public function  getDSN()
    {
        $config=$this->config;
        $dsn = "mysql:host=".$config['DB_HOST'].";port=".$config['DB_PORT'].";dbname=".$config['DB_NAME'];
        return $dsn;
    }
}