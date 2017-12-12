<?php

namespace toolbox\util;

use ReflectionMethod;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/20
 * Time: 10:01
 */
class MyRedis
{
    static $myRedis;
    private $redis;
    private $server;
    private $port;

    /**
     * MyRedis constructor.
     * @param $server
     * @param $port
     */
    public function __construct($server, $port, $pass)
    {
        $this->server = $server;
        $this->port = $port;
        $this->pass = $pass;
        $this->redis = new \Redis();
//        $redis->lPush();
        DLog("test");
        $result = $this->redis->connect($this->server, $this->port);
        !$result && DLog("无法连接Redis {$this->server} {$this->port}");
        $this->redis->setOption(\Redis::OPT_READ_TIMEOUT, -1);//设置使得brPop永不超时
        if ($pass) {
            $this->redis->auth($pass);
        }
    }

    /**
     * @title getMyRedis
     * @description
     * @author: daniel
     * @param string $server
     * @param int $port
     * @param null $pass
     * @return MyRedis
     */
    public static function getMyRedis($server = "127.0.0.1", $port = 6379, $pass = null)
    {
        if (!isset(self::$myRedis[$server])||!self::$myRedis[$server]) {
            self::$myRedis[$server] = new MyRedis($server, $port, $pass);
        }
        return self::$myRedis[$server];
    }

    
    /**
     *  将消息推入队列
     * @title pushToList
     * @description
     * @author: daniel
     * @param $listName
     * @param $data
     * @param bool $index
     * @param int $clearBlockingCount 队列中未处理数据超过数量,自动清除队列
     */
    public function pushToList($listName, $data, $index = false, $clearBlockingCount = 0)
    {
        if ($clearBlockingCount) {
            $length = $this->redis->lLen($listName);
            $length > $clearBlockingCount && $this->redis->del($listName);
        }
        //将一个或多个值value插入到列表key的表头。
        return $this->redis->rPush($listName, $data);
    }

    public function popFromList($listName, $fromHead = true)
    {
        //Returns and removes the first element of the list.
        if ($fromHead) {
            return $this->redis->lPop($listName);
        } else {
            //Returns and removes the last element of the list.
            return $this->redis->rPop($listName);
        }
    }

    /**
     * @title blockingPopFromList
     * @description
     * @author: daniel
     * @param $listName
     * @param int $timeout 超时为3天.最大值为2000000000,
     * @param bool $fromHead
     * @return
     */
    public function blockingPopFromList($listName, $timeout = 0, $fromHead = true)
    {
        //Returns and removes the first element of the list.
        if ($fromHead) {
            $data = $this->redis->blPop($listName, $timeout);
        } else {
            $data = $this->redis->brPop($listName, $timeout);
        }
        if ($data && is_array($data)) {
            return $data[1];
        } else {
            if (IS_DEBUG) {
                DLog($data);
            }
            return false;
        }
    }


    public function listLength($listName)
    {
        return $this->redis->lLen($listName);
    }

    /**
     * @title getFromSet
     * @description
     * @author: daniel
     * @param $setName
     * @param $key
     * @return array
     */
    public function getFromSet($setName, $key)
    {
        return $this->redis->sMembers($setName . "_" . $key);
    }

    /**
     * @title addToSet
     * @description 将数据添加到一个Set中
     * @author: daniel
     * @param $setName
     * @param $key
     * @param $data
     * @return int
     */
    public function addToSet($setName, $key, $data)
    {
        return $this->redis->sAdd($setName . "_" . $key, $data);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key, $value, $timeout = 0)
    {
        $this->redis->set($key, $value, $timeout);
    }

    /**
     * @title subscribe
     * @description
     * @author: daniel
     * @param $channels
     * @param $callback array|string $callback either a string or an array($instance, 'method_name'). The callback function receives 3 parameters: the redis instance, the channel name, and the message.
     */
    public function subscribe($channels, $callback)
    {
        if (is_string($channels)) {
            $channels = array($channels);
        }
        $this->redis->subscribe($channels, $callback);
    }

    /**
     * @title subscribe
     * @description
     * @author: daniel
     * @param $channels
     * @message string  string
     */
    public function publish($channel, $message)
    {
        $this->redis->publish($channel, $message);
    }

    public function setToMap($mapName, $key, $value)
    {
        $this->redis->hSet($mapName, $key, $value);
    }

    public function getFromMap($mapName, $key)
    {
        return $this->redis->hGet($mapName, $key);
    }

    public function incr($key, $prefix = "", $value = false)
    {
        $key = $prefix . "-" . $key;
        if ($value) {
            if (is_float($value)) {
                return $this->redis->incrByFloat($key, $value);
            } else {
                return $this->redis->incrBy($key, $value);
            }
        } else {
            return $this->redis->incr($key);
        }
    }

    public function getCountValues($prefix)
    {
        $keys = $this->redis->keys($prefix . "*");
        $array = array();
        foreach ($keys as $key) {
            $realKey = str_replace($prefix . "-", "", $key);
            $array[$realKey] = $this->redis->get($key);
        }
        return $array;
    }


    /**
     * @title rawCommand
     * @description
     * @author: daniel
     * @param $param string
     */
    public function rawCommand($cmd, $key, $param)
    {
        $paramArr = array_merge([$cmd, $key], $param);
        $method = new ReflectionMethod('\Redis', 'rawCommand');
        $res = $method->invokeArgs($this->redis, $paramArr);
        return $res;
    }


    /**
     *
     * @title geoAdd
     * @description
     * @author: daniel
     * @param $key
     * @param $points
     */
    public function geoAdd($key, $points = [])
    {
        $paramArr = [];
        foreach ($points as $point) {
            $paramArr[] = $point["lng"];
            $paramArr[] = $point["lat"];
            $paramArr[] = $point["name"];

        }
        $this->rawCommand("geoadd", $key, $paramArr);
    }


    public function geoPos($key, $member)
    {

    }


}
