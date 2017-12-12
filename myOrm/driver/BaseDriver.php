<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2015/11/25
 * Time: 15:44
 */
namespace toolbox\myOrm\driver;


use toolbox\myOrm\MyPDOStatement;

abstract class BaseDriver
{
    protected $config;
    protected $pdo;
    private $defaultConfig = array(
        'PDO_OPTIONS'=>array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_STATEMENT_CLASS => array("toolbox\myOrm\MyPDOStatement", array()),
        )
    );

    public function __construct($config)
    {
        $config = array_merge($this->defaultConfig,$config);
        $this->config = $config;
        $this->pdo = new \PDO($this->getDSN(), $config['DB_USER'], $config['DB_PWD'], isset($config['PDO_OPTIONS']) ? $config['PDO_OPTIONS'] : null);
    }

    public function getPDO()
    {
        return $this->pdo;
    }

    public function query($sql)
    {
        $return = $this->pdo->query($sql);
        return $return->columnCount()==0? null:$return->fetchAll(\PDO::FETCH_ASSOC);
    }

    public abstract function  getDSN();

} 