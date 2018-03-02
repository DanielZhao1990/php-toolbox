<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9
 * Time: 15:18
 */

namespace toolbox\loader;

use think\App;

class TestLoader
{

    /**
     * Tp5模块单元测试的加载器，加载环境、配置以及common方法等
     * @description
     * @author: daniel
     * @param $moduleDir
     */
    public static function tp5ModuleLoader($moduleDir,$env=false)
    {
        define('APP_PATH', realpath($moduleDir . '/../'));
        require APP_PATH."/../thinkphp/base.php";
        App::initCommon();
        if ($env) //传入环境则加载
        {
            EnvLoader::envLoad($moduleDir . "/env",$env);
        }
        is_file($moduleDir . "/config.php") && \think\Config::load($moduleDir . "/config.php");
        is_file($moduleDir . "/common.php") && require_once $moduleDir . "/common.php";
    }

}
