<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/6
 * Time: 11:07
 */

namespace toolbox\util;


class RunParamHelper
{
    public static function getRunBaseParam()
    {
        $opt=getopt("DE:");
        return $opt;
    }

    /**
     * @title getParam
     * @description php xxx --delOld 使用 getParam("delOld")判断是否传值,如果要传入值，则必须使用 -d=xxx --ddd=xxx
     * @author: daniel
     * @param string $key 所取key
     * @param bool $withValue 是否跟随值，比如 需要返回数字字符串等， 则传true， 如果只需要返回true Or false.则不传
     * @param bool $defaultValue 跟值时,如果运行脚本没有传入参数，默认返回值
     * @return mixed
     */
    public static function getParam($key, $withValue=false,$defaultValue=false)
    {
        $code=$withValue? ":":"";
        if (strlen($key)>1){
            $opt=getopt("",array("$key$code"));
        }else{
            $opt=getopt("$key$code");
        }
        if ($withValue&&isset($opt[$key]))//是否传入参数 例如 -d=123  --delOld=123
        {
            return $opt[$key];
        }else if (!$withValue&&isset($opt[$key]))//是否设置了项 比如 --delOld  -d
        {
            return true;
        }else{
            return $defaultValue;
        }
    }

    public static function getEnv()
    {
        $param=self::getRunBaseParam();
        if (isset($param["E"]))
        {
            return $param["E"];
        }else if (defined("APP_ENV")){
            return APP_ENV;
        }else{
            return "product";
        }
    }

    public static function isDebug()
    {
        $param=self::getRunBaseParam();
        if (isset($param["D"]))
        {
            return true;
        }else{
            return false;
        }
    }

    /**
     * @title loadEnvConfig
     * @description 根据参数环境读取参数
     * @author: daniel
     * @param $configDir
     */
    public static function loadEnvConfig($configDir)
    {
        $env=self::getEnv();
        $commonConfig=$configDir."/common_config.php";
        if (is_file($commonConfig)){
            require_once $commonConfig;
        }
        $config_file=$configDir."/$env.php";
        DLog($config_file);
        if (is_file($config_file)){
            echo "加载$env 环境遍历\n";
            require_once $config_file;
        }
    }
}