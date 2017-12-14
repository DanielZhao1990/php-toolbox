<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20
 * Time: 11:40
 */

namespace toolbox\util;


class TableDataCompareUtil
{
    /**
     * @title compareData
     * @description 比较两个表数据是否相同，返回比较结果,包括增删改
     *
     * @author: daniel
     * @param $srcData
     * @param $desData
     * @return array  return array(
     *  "del" => $del,
     *   "add" => $add,
     *   "update" => $update,
     *   );
     */

    public static function compareData($srcData, $desData)
    {
        $del = array();
        $add = array();
        $update = array();
        foreach ($srcData as $key => $srcItem) {
            if (isset($desData[$key]))//比较是否为更新内容
            {
                if (self::compareItemArray($srcItem, $desData[$key]))//如果数组相同
                {
                    continue;
                } else {
                    $update[$key] = $srcItem;
                }
            } else {// 新增数据
                $add[$key] = $srcItem;
            }
        }
        $addCount = count($add);
        $srcCount = count($srcData);
        $desCount = count($desData);
        if ($srcCount - $addCount !== $desCount)//如果两个数据数量相同，则没有删除
        {
            foreach ($desData as $key => $desItem) {
                !isset($srcData[$key]) && $del[$key] = $desItem;
            }
        }
        return array(
            "del" => $del,
            "add" => $add,
            "update" => $update,
        );
    }

    /**
     * @title compareItemArray
     * @description  比较俩数组是否相同，包括key　Value，并不递归比较
     * @author: daniel
     * @param $srcData
     * @param $desData
     * @return bool
     */
    public static function compareItemArray($srcData, $desData)
    {
        foreach ($srcData as $key => $srcItem) {
            if (isset($desData[$key]))//存在
            {
                if ($desData[$key] == $srcItem) {
                    continue;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return true;
    }
}