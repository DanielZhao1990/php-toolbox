<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6 0006
 * Time: 15:35
 */

namespace toolbox\loader;


class EnvLoader
{
    /**
     * @title envLoad
     * @description
     * @author: daniel
     * @param $envDir string 环境目录地址
     * @param $env string 环境名,可以不传入，不传入时使用 APP_COMMAND_ENV 常量
     * @param string $initialConfig 最先初始化的公共配置的目录 ,默认为$envDir/initial_config.php
     */
    public static function envLoad($envDir, $env = null, $initialConfig = "initial_config")
    {
        is_file($envDir . DIRECTORY_SEPARATOR . "/$initialConfig.php") && require_once $envDir . DIRECTORY_SEPARATOR . "/$initialConfig.php";
        $env = $env ? $env : APP_COMMAND_ENV;
        $env_file = $envDir . DIRECTORY_SEPARATOR . $env . ".php";
        if (is_file($env_file)) {
            require_once $env_file;
        } else {
            echo "未定义的环境文件 $env_file\n";
        }
    }
}