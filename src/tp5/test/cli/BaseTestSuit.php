<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/7
 * Time: 14:25
 */

namespace toolbox\tp5\test\cli;

use ReflectionClass;

class BaseTestSuit extends \PHPUnit_Framework_TestSuite
{
    protected $testDir = "";

    public function __construct($theClass = '', $name = '')
    {
        $currentClassFileName = (new ReflectionClass(get_class($this)))->getFileName();
        $dir = dirname($currentClassFileName);
        $this->testDir = $dir;
        $thinkDir = __DIR__ . "/../../";
        parent::__construct($theClass, $name);

    }

    protected function initialTestSuit()
    {
        $this->addTestSuite('\app\calculationGps\test\GpsCalTest');
    }


}