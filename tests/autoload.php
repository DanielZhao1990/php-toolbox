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
define('TEST_PATH', __DIR__ . '/');

//做library时，引用项目的
$libComposerLoader = "../../../autoload.php";
// project开发时，使用当前项目的loader
$projectComposerLoader = __DIR__."/../vendor/autoload.php";
is_file($libComposerLoader) && require_once $libComposerLoader;
is_file($projectComposerLoader) && require_once $projectComposerLoader;

