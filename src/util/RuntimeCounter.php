<?php

namespace toolbox\util;

use toolbox\format\FormatUtil;

/**
 *
 *
 * Class MemoryCounter
 * @package toolbox\util
 */
class RuntimeCounter extends BaseCounter
{
    private $tag;
    private $start;
    private $end;
    private $lastLogTime;

    static $instances = [];

    public static function instance($tag="RuntimeCounter", $logType = self::LOT_TYPE_CONSOLE)
    {
        if (!isset(self::$instances[$tag])) {
            self::$instances[$tag] = new RuntimeCounter($tag, $logType);
        }
        return self::$instances[$tag];
    }

    public function __construct($tag, $logType)
    {
        parent::__construct();
        $this->logType = $logType;
        $this->tag = $tag;
    }


    /**
     * 打印开始计时
     * @description
     * @author: daniel
     */
   public function start()
    {
        $this->start = self::microTime();
        $this->lastLogTime = $this->start;
        $this->printLog("--------------- 开始 $this->tag 计时 --------------- ");
    }


    /**
     * 记录日志
     * @description
     * @author: daniel
     * @param $tag
     * @return float
     */
    function log($tag)
    {
        $current = self::microTime();
        $time = $current - $this->lastLogTime;
        $this->printLog($tag . "操作耗时 " . ($time) . " 毫秒");
        $this->lastLogTime = $current;
        return $time;
    }

    function end()
    {
        $this->end = self::microTime();
        $this->printLog($this->tag . " 操作总耗时 " . ($this->end - $this->start) . " 毫秒");
        $this->printLog("--------------- 结束 $this->tag 计时 --------------- ");
    }


    public static function microTime()
    {
        //返回当前的毫秒时间戳
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}