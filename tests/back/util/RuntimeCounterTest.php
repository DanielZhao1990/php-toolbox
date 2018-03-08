<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace tests;

use toolbox\util\RuntimeCounter;

class RuntimeCounterTest extends \PHPUnit\Framework\TestCase
{
    public function testBasicExample()
    {
        RuntimeCounter::instance()->start();
        sleep(1);
        RuntimeCounter::instance()->log("测试1");
        sleep(0.5);
        RuntimeCounter::instance()->log("测试2");
        RuntimeCounter::instance()->end();
    }



}