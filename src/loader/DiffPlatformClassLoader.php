<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/21 0021
 * Time: 20:55
 */

namespace toolbox\loader;
/**
 * 自动加载类。
 * 使用概要。
 * 1. 设置搜索根目录
 *        require_once './toolbox/Autoloader.php';
 *       \toolbox\Autoloader::setRootPath(__DIR__);//设置当前目录为默认搜索目录
 * 2. 将相应的类放到相应的目录下
 *  eg: 项目根目录为\project
 *    命名空间为A的c类应为 \project\A\c
 *  or: 设置根目录为 \A
 * @author walkor<walkor@workerman.net>
 */
class DiffPlatformClassLoader
{
    static $classExt = ".php";
    static $hasInitial=false;
    protected static $diff_platform = array(
        "GatewayWorker",
        "Workerman",
        "PHPSocketIO"
    );
    // 应用的初始化目录，作为加载类文件的参考目录
    protected static $_appInitPath = [];

    /**
     * 根据命名空间加载文件
     * @param string $className
     * @return boolean
     */
    public static function loadByNamespace($className)
    {
        foreach (self::$_appInitPath as $classPrefix => $classDir) {
            if (strpos($className,$classPrefix) === 0) {
                $class_file = self::tryToFindPath($className,$classDir);
                if ($class_file !== false) {
                    require_once $class_file;
                    return class_exists($className, false);
                }
            }
        }
        return false;
    }

    /**
     * 尝试查找类
     * @param $name
     * @param $ext
     * @return bool|string
     */
    static    function  tryToFindPath($className, $classDir)
    {
        // 相对路径
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $classInfoArr = explode(DIRECTORY_SEPARATOR, $class_path);
        $classInfoArr[0]=$classInfoArr[0] . "-" . self::getOperateSystemStr();
        $class_path = implode(DIRECTORY_SEPARATOR, $classInfoArr);
        // 先尝试在应用目录寻找文件
        $class_file = $classDir . DIRECTORY_SEPARATOR . $class_path . self::$classExt;
        return is_file($class_file) ? $class_file : false;
    }

    public static function getOperateSystemStr()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return "Win";
        } else {
            return "Linux";
        }
    }

    /**
     * like workerman\a\b 传入workerman
     * @title addClassloader
     * @description
     * @author: daniel
     * @param $classDir
     * @param $classPrefix
     */
    public static function addClassloader($classDir, $classPrefix)
    {
        if (!self::$hasInitial)
        {
            // 设置类自动加载回调函数
            spl_autoload_register('toolbox\loader\DiffPlatformClassLoader::loadByNamespace');
            self::$hasInitial=true;
        }
        self::$_appInitPath[$classPrefix."\\"] = $classDir;
    }
}


