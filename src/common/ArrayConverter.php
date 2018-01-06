<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/6 0006
 * Time: 16:42
 */

namespace toolbox\common;


use toolbox\util\StringUtil;

class ArrayConverter
{
    const CONVERTER_TO_HUMP = "CONVERT_TO_HUMP";
    const CONVERTER_TO_UNDERLINE = "CONVERT_TO_UNDERLINE";

    /**
     * 批量转换数组的key
     * @title convertArrayKey
     * @description
     * @author: daniel
     * @param $srcArrayItem
     * @param $map array 转换映射值
     * @return mixed
     */
    public static function convertArrayKey($srcArrayItem, $map)
    {
        foreach ($map as $srcKey => $desKey) {
            if (isset($srcArrayItem[$srcKey])) {
                $srcArrayItem[$desKey] = $srcArrayItem[$srcKey];
                unset($srcArrayItem[$desKey]);
            }
        }
        return $srcArrayItem;
    }



    private static function convertArrayKeyToHumpOrUnderLine($type, $srcArrayItem, $map = [], $except = [])
    {
        $ret = [];
        foreach ($except as $key) {
            if (isset($srcArrayItem[$key])) {
                $ret[$key] = $srcArrayItem[$key];
                unset($srcArrayItem[$key]);
            }
        }
        foreach ($map as $mapSrcKey => $mapDesKey) {
            if (isset($srcArrayItem[$mapSrcKey])) {
                $ret[$mapDesKey] = $srcArrayItem[$mapSrcKey];
                unset($srcArrayItem[$mapSrcKey]);
            }
        }
        foreach ($srcArrayItem as $srcKey => $srcValue) {
            if ($type == "2hump") {
                $desKey = StringUtil::underline2Hump($srcKey);
            } else {
                $desKey = StringUtil::hump2Underline($srcKey);
            }
            $ret[$desKey] = $srcArrayItem[$srcKey];
        }
        return $ret;
    }


    /**
     *  将数组的索引从下划线式转换为驼峰式
     * @title convertArrayKeyToHump
     * @description
     * @author: daniel
     * @param $srcArrayItem
     * @param array $map 特殊转换映射
     * @param array $except 不进行转换字段
     * @return array
     */
    public static function convertArrayKeyToHump($srcArrayItem, $map = [], $except = [])
    {
        return self::convertArrayKeyToHumpOrUnderLine("2underline", $srcArrayItem, $map, $except);
    }

    /**
     * 将数组的索引从下划线式转换为驼峰式
     * @title convertArrayKeyToUnderline
     * @description
     * @author: daniel
     * @param $srcArrayItem
     * @param array $map 特殊转换映射
     * @param array $except 不进行转换字段
     * @return array
     */
    public static function convertArrayKeyToUnderline($srcArrayItem, $map = [], $except = [])
    {
        return self::convertArrayKeyToHumpOrUnderLine("2underline", $srcArrayItem, $map, $except);
    }
}