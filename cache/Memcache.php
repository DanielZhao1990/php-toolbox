<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace toolbox\cache;
class Memcache {
    private $mem;
    public $expireTime=900;
    //put your code here
    public function __construct($ip,$port) {
        $this->mem = new \Memcache();
        $this->ip=$ip;
        $this->port=$port;
        if (!$this->connect())
        {
            throw new \Exception("Memcache无法连接到 $this->ip");
        }
    }

    public function set($key, $value, $expire=0) {
        if(!$expire)
        {
            $expire=$this->expireTime;
        }
        try {
            $this->mem->set($key, $value, 0,$expire);
        } catch (\Exception $ex) {
            $this->connect();
            $this->mem->set($key, $value, 0,$expire);
        }
    }

    public function get($key) {
        try {
            return $this->mem->get($key);
        } catch (\Exception $ex) {
            $this->connect();
            return $this->mem->get($key);
        }
    }
    public function delete($key) {
        try {
            return $this->mem->delete($key);
        } catch (\Exception $ex) {
            $this->connect();
            return $this->mem->delete($key);
        }
    }
    public function connect()
    {
       return  $this->mem->connect($this->ip, $this->port);
    }
}
