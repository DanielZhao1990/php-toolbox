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
     * @var array
     */
    public static $defaultEnvMap=[
        "local"=>[
            "localhost",
            "127.0.0.1",
            //匹配 192.168.0.* ip段
            "/^192\.168\.0\..*/",
        ],
        "dev"=>[
            "test.51zsqc.com",
            "ceshi.51zsqc.com",
            //匹配 192.168.90.* ip段
            "/^192\.168\.90\..*/",
            // 匹配任意51zsqc.com的二级域名
            "/^(\w+)\.51zsqc\.com/"
        ],
        "product"=>[
            // 匹配51zsqc.com
            "51zsqc.com",
            "192.168.90.22",
            "192.168.90.24",
        ]
    ];





    /**
     * @description
     * @author: daniel
     * @param $maps array 请参照 EnvLoader::$demoMap
     */
    public static function getEnvByHost($maps,$defaultEnv="local")
    {
        $curHost=$_SERVER["HTTP_HOST"];
        foreach ($maps as $env=>$hosts) {
            foreach ($hosts as $host) {
                if (strpos($host,"/")!==0)// 不是正则，直接比较
                {
                    if ($curHost===$host)
                    {
                       return $env;
                    }
                }
            }
        }
        foreach ($maps as $env=>$hosts) {
            foreach ($hosts as $host) {
                if (strpos($host,"/")===0)//如果是正则表达式，使用正则匹配
                {
                   if (preg_match($host, $curHost))
                   {
                       return $env;
                   }
                }
            }
        }
        return $defaultEnv;
    }

    /**
     * @title envLoad
     * @description
     * @author: daniel
     * @param $envDir string 环境目录地址
     * @param $env string 环境名,可以不传入，不传入时使用 APP_COMMAND_ENV 常量
     * @param string $initialConfig 最先初始化的公共配置的目录 ,默认为$envDir/initial_config.php
     */
    public static function envLoad($envDir, $env = null, $initialConfig = "common_config")
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