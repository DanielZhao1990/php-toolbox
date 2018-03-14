<?php
namespace toolbox\file;
use toolbox\encode\SimpleEncoder;
use toolbox\system\Environment;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/12
 * Time: 13:17
 */
class FileHelper
{
    public static function rename($src,$des)
    {
        rename($src,$des);
    }

    public static function extension($path)
    {
        $pathinfo=pathinfo($path);
        if ($pathinfo)
        {
            return $pathinfo["extension"];
        }
    }

    /**
     * 从一个目录中读取所以的子文件以及目录,并自动转换编码。适合windows系统
     * @title readFileNames
     * @description
     * @author: daniel
     * @param $dirPath
     * @param bool $utf8Result
     * @param bool $totalPath
     * @return bool
     */
    static function readSubFiles($dirPath,$utf8Result=true,$totalPath=false)
    {
        if (Environment::isWindows()) {
            $dirPath = SimpleEncoder::tryConvertToGBK($dirPath);
        }
        // 打开目录，然后读取其内容
        if (is_dir($dirPath)) {
            $files = scandir($dirPath);
            $files=self::removeNoNeedFile($files);
            if ($totalPath)
            {
                foreach ($files as &$file) {
                    $file=$dirPath."/".$file;
                }
            }
            if ($utf8Result&&Environment::isWindows()) {
                $files = SimpleEncoder::tryConvertToUTF8($files);
            }
            return $files;
        }
        return false;
    }


    /**
     * 移除文件数组中的. 以及 ..
     * @title removeNoNeedFile
     * @author: daniel
     * @param $files
     * @param null $dirPath
     * @return mixed
     */
    static function removeNoNeedFile($files)
    {
        foreach ($files as $key => $file) {
            if ($file == "." || $file == "..") {
                unset($files[$key]);
            }
        }
        return $files;
    }


    static function file_exists($filePath){
        //判断环境
            $encode = SimpleEncoder::mb_detect_encoding($filePath);
            if ($encode == "UTF-8") {
                $filePath = iconv("UTF-8", "GB2312", $filePath);
            }
        if(file_exists($filePath)){
            return true;
        }else{
            return false;
        }
    }

}