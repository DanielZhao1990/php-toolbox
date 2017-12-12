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


    public static function instance($tag)
    {
        $runtime = new RuntimeCounter($tag);
        return $runtime;
    }

    public function __construct($tag)
    {
        $this->tag = $tag;
    }


    function start($mark=false)
    {
        $this->start = self::microTime();
        $this->lastLogTime =   $this->start;
        if(!$mark){
            self::printLog("--------------- 开始 $this->tag 计时 --------------- ");
        }else{
            self::printTestLog("--------------- 开始 $this->tag 计时 --------------- ");
        }
    }

    function tag($tag)
    {
        $this->tag = $tag;
    }

<<<<<<< HEAD
    function log($tag,$flag=false)
=======
    function log($tag,$mark=false)
>>>>>>> 7876a44dbf7799c19dbf229f3cf8fe816c33adb6
    {
        if($flag){
            self::printTestLog($tag);
        }
        $current = self::microTime();
        $time = $current - $this->lastLogTime;
        if(!$mark){
            self::printLog($tag . "操作耗时 " . ($time) . " 毫秒");
        }else{
            self::printTestLog($tag . "操作耗时 " . ($time) . " 毫秒");
        }
        $this->lastLogTime = $current;
        return $time;
    }

    function end($mark=false)
    {
        $this->end = self::microTime();
        if(!$mark){
            self::printLog($this->tag . " 操作总耗时 " . ($this->end - $this->start) . " 毫秒");
            self::printLog("--------------- 结束 $this->tag 计时 --------------- ");
        }else{
            self::printTestLog($this->tag . " 操作总耗时 " . ($this->end - $this->start) . " 毫秒");
            self::printTestLog("--------------- 结束 $this->tag 计时 --------------- ");
        }
    }


    public static function microTime()
    {
        //返回当前的毫秒时间戳
            list($msec, $sec) = explode(' ', microtime());
            return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
}