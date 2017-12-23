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
class Autoloader
{
    private static $classEXT = array(
        ".php",
        ".class.php",
    );
    /**
     * 相应命名空间的搜索路径.
     * 比如: toolbox =>  "E:\project",那么请将toolbox放到E:\project下。如果不设置，则会默认使用上层目录作为根目录
     * @var array
     */
    public static $searchDir = array();
    // 应用的初始化目录，作为加载类文件的参考目录
    protected static $_appInitPath = "";
    protected static $_libInitPath = __DIR__;

    /**
     * 设置应用初始化目录
     * @param string $rootPath
     * @return void
     */
    public static function setRootPath($rootPath)
    {
        self::$_appInitPath = $rootPath;
        // 设置类自动加载回调函数
        spl_autoload_register(__CLASS__ . '::loadByNamespace');
    }

    /**
     * 根据命名空间加载文件
     * @param string $name
     * @return boolean
     */
    public static function loadByNamespace($name)
    {
        $class_file = null;
        /**
         * 尝试所有的后缀进行查找
         */
        foreach (self::$classEXT as $ext) {
            $class_file = self::tryToFindPath($name, $ext);
            if ($class_file !== false) {
                break;
            }
        }
        if (!$class_file) {
            return false;
        } else {
            require_once $class_file;
            if (class_exists($name, false)) {
                return true;
            }
        }
    }

    /**
     * 尝试查找类
     * @param $name
     * @param $ext
     * @return bool|string
     */
    static function tryToFindPath($name, $ext)
    {
        // 相对路径
        $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $name);
        // 如果是Workerman命名空间，则在当前目录寻找类文件
        $class_file = false;
        // 先尝试在应用目录寻找文件
        if (self::$_appInitPath) {
            $class_file = self::$_appInitPath . DIRECTORY_SEPARATOR . $class_path . $ext;
        }
        return is_file($class_file) ? $class_file : false;
    }

}

