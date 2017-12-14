<?php

/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/1/6
 * Time: 16:03
 */
namespace toolbox\common;
class ArrayUtil
{
    /**
     * @title id2key
     * @description 将数组转换为按照$fieldName为key的结构。必须报站$fieldName唯一性
     * @author: daniel
     * @param $array
     * @param string $fieldName
     * @param bool $del
     * @return array
     */
    public static function id2key($array, $fieldName = "id", $del = true)
    {
        $re = array();
        foreach ($array as $value) {
            $re[$value[$fieldName]] = $value;
            if ($del) {
                unset($re[$fieldName][$fieldName]);
            }
        }
        return $re;
    }

    /**
     * 取出某一列或某多列的数据
     * @title getColumns
     * @description
     * @author: daniel
     * @param $array 二位数组
     * @param $fieldName
     * @return array 的数据
     */
    public static function getColumns($array, $fieldName)
    {
        if (is_array($fieldName)) {
            $re = array();
            foreach ($array as $oldItem) {
                foreach ($fieldName as $key) {
                    $item[$key] = $oldItem[$key];
                }
                $re[] = $item;

            }
            return $re;
        } else {
            $re = array();
            foreach ($array as $oldItem) {
                $re[] = $oldItem[$fieldName];
            }
            return $re;
        }
    }

    /**
     * 取出某一列或某多列的数据
     * @title getColumns
     * @description
     * @author: daniel
     * @param $array
     * @param $fieldName
     * @return array 某一列的数据
     */
    public static function getColumnsFromSingleArr($array, $fieldName)
    {
        foreach ($fieldName as $key) {
            $item[$key] = $array[$key];
        }
        return $item;
    }


    /**
     * @title groupArray
     * @description
     * @author: liuxing
     * @param $array
     * @param $string
     * @return array
     */
    public static function groupArray($array, $string)
    {
        $return = [];
        foreach ($array as $item) {
            $return[$item[$string]][] = $item;
        }
        return $return;
    }

    /**
     * 检查给定字段是否都存在
     * @title checkFieldExists
     * @description
     * @author: daniel
     * @param $arr
     * @param array $fileds
     * @return bool
     */
    public static function checkFieldExists($arr,$fileds=[])
    {
        foreach ($fileds as $filed) {
            if (!isset($arr[$filed]))
            {
                return false;
            }
        }
        return true;
    }


    /**
     * 比较两个数组指定的field是否全部相等
     * @title compareField
     * @description
     * @author: daniel
     * @param $old
     * @param $new
     * @param $fields
     */
    public static function compareField($old, $new, $fields)
    {
        foreach ($fields as $field) {
            if (isset($old[$field])&&isset($new[$field]))
            {
                if ( $old[$field]!==$new[$field])
                {
                    return false;
                }
            }
        }
        return true;
    }
}