<?php
namespace toolbox\encode;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/12
 * Time: 13:21
 */
class SimpleEncoder
{

    /**
     *
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
            $encode = mb_detect_encoding($data, array("ASCII", 'UTF-8', "GB2312", "GBK"));
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
            $encode = mb_detect_encoding($data, array("ASCII", "GBK", 'UTF-8', "GB2312"));
            if (in_array($encode, ["GBK", "CP936", "GB2312"])) {
                return iconv("GBK", "UTF-8", $data);
            }
        }
        return $data;
    }



}