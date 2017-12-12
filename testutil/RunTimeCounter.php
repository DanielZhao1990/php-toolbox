<?php
namespace toolbox\testutil;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-03-23
 * Time: 11:41
 */
class RunTimeCounter
{
    static private $startTime = 0;
    static private $countTime = array();
    private static $tag="";

    static function start($tag="")
    {
        self::$tag=$tag;
        self::$startTime = microtime(true);
        self::$countTime = array();
    }

    static function count($tag = null)
    {
        if ($tag) {
            self::$countTime[$tag] = microtime(true);
        } else {
            self::$countTime[] = microtime(true);
        }
    }

    static function printResult()
    {
        if (self::$countTime) {
            foreach (self::$countTime as $key => $count) {
                if (is_int($key)) {
                    self::log("第 $key 次计时 " . ($count - self::$startTime));
                } else {
                    self::log("$key 计时 " . ($count - self::$startTime));
                }
            }
        }
        self::log(self::$tag. "  总执行时间  " . (microtime(true) - self::$startTime));
    }

    static function log($msg)
    {
        L($msg);
    }
}