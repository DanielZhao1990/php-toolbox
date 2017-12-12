<?php
namespace toolbox\socket;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/1
 * Time: 9:53
 */
class TcpConnection extends \Workerman\Connection\AsyncTcpConnection
{

    public function getStatus()
    {
        return $this->_status;
    }

    public function hasConnected()
    {
        return $this->_status===self::STATUS_ESTABLISH;
    }
    
    public function needConnect()
    {
        return !in_array($this->_status,array(self::STATUS_CONNECTING,self::STATUS_ESTABLISH));
    }



}