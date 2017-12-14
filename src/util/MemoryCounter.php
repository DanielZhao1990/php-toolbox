<?php
namespace toolbox\util;

use toolbox\format\FormatUtil;

/**
 *
 *
 * Class MemoryCounter
 * @package toolbox\util
 */
class MemoryCounter
{
//$counter=new MemoryCounter("Insert to DB");
//$counter->start();
//$counter->end();
    private $tag;
    private $startMem;
    private $endMem;

    public function __construct($tag)
    {
        $this->tag = $tag;
    }


    function start()
    {
        $this->startMem = memory_get_usage();
    }

    function tag($tag)
    {
        $this->tag = $tag;
    }

    function log($tag)
    {
        $mem = memory_get_usage();
        L($tag . " 操作新增内存 " . ($mem - $this->startMem) . " 字节");
    }

    function end()
    {
        $this->endMem = memory_get_usage();
        L($this->tag . " 操作新增内存 " . ($this->endMem - $this->startMem) . " 字节");
    }

    static function showMemoryInfo()
    {
        $usageStr = self::getMemoryInfo();
        L(" 当前占用内存 $usageStr 字节");
    }

    /**
     * @return string
     */
    public static function getMemoryInfo()
    {
        $usage = memory_get_usage();
        $usageStr = FormatUtil::byteFormat($usage);
        return $usageStr;
    }
}