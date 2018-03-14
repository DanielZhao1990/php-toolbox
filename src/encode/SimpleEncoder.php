<?php
namespace toolbox\encode;
/**
 *
 * GBK=CP936
 * GB2312=EUC-CN
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/12
 * Time: 13:21
 */
class SimpleEncoder
{
    static $chineseEncode=['GBK','CP936','EUC-CN',"GB2312"];

    public static function mb_detect_encoding($string)
    {
        return mb_detect_encoding($string,['ASCII','UTF-8','GBK','CP936','EUC-CN']);
    }
    /**
     * @title tryConvertToGBK
     * @description
     * @author: daniel
     * @param $data array|string
     * @return mixed 转换以后的数据
     */
    static function tryConvertToGBK($data=[])
    {
        if (is_array($data)) {
            foreach ($data as &$item) {
                $item =  self::tryConvertToGBK($item);
            }
            return $data;
        } else {
            $encode = mb_detect_encoding($data, ['ASCII','UTF-8','GBK','CP936','EUC-CN']);
            if ($encode == "UTF-8") {
                return iconv("UTF-8", "GBK", $data);
            }
        }
        return $data;
    }

    /**
     * @title tryConvertToUTF8
     * @description
     * @author: daniel
     * @param $data
     * @return mixed
     */
    static function tryConvertToUTF8($data)
    {
        if (is_array($data)) {
            foreach ($data as &$item) {
                $item = self::tryConvertToUTF8($item);
            }
            return $data;
        } else {
            $encode = mb_detect_encoding($data, ['ASCII','UTF-8','GBK','CP936','EUC-CN']);
            if (in_array($encode, ["GBK", "CP936", "GB2312"])) {
                return iconv("GBK", "UTF-8", $data);
            }
        }
        return $data;
    }



}