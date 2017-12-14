<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-03-25
 * Time: 9:43
 */

namespace toolbox\util;


class DateUtil
{
    const TYPE_DAY = "day";
    const TYPE_YEAR = "year";


    /**
     * 对日期进行加减
     * @param $date
     * @param $value
     * @param $type
     * @param null $format 如果需要格式化，设置"Y-m-d"等
     * @return bool|int
     */
    static function date_calc_simple($date, $value, $type, $format = null)
    {
        if (is_int($date))
            $date = date("Y-m-d h:i:s");
        $resultTime = strtotime("$date +$value $type");
        return $format ? date($format, $resultTime) : $resultTime;
    }

    /**
     * 对日期进行加减
     * @param $date
     * @param $operation
     * @param null $format 如果需要格式化，设置"Y-m-d"等
     * @return bool|int
     */
    static function date_calc($date, $operation,$format = null)
    {
        if (is_int($date))
            $date = date("Y-m-d h:i:s");
        $resultTime = strtotime("$date $operation");
        return $format ? date($format, $resultTime) : $resultTime;
    }

    public static function currentDate()
    {
        return date("Y-m-d");
    }

}