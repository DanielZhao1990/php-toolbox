<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 11:12
 */

namespace toolbox\loader;


class Loader
{
    /**
     * @title autoload
     * @description
     * @author: daniel
     * @param $path string 自动加载$path目录下的文件
     */
    static function autoload($path)
    {
        $files = scandir($path);
        foreach ($files as $fileName) {
            if (strpos($fileName, ".php")) {
                require_once $path . "/$fileName";
            }
        }
    }
}