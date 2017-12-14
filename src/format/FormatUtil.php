<?php


namespace toolbox\format;
class FormatUtil
{
    const VALUE_KB = 1024;
    const VALUE_MB = 1048576;
    const VALUE_GB = 1073741824;

    public static function int2TimeStamp($int)
    {
        return date('Y-m-d H:i:s', $int);
    }

    public static function TimeStamp2int($str)
    {
        return strtotime($str);
    }

    public static function byteFormat($value)
    {
        if ($value <= self::VALUE_KB) {
            $valueStr = $value . " byte";
        } elseif ($value <= self::VALUE_MB) {
            $valueStr = number_format($value / self::VALUE_KB, 4) . " KB";
        } elseif ($value <= self::VALUE_GB) {
            $valueStr = number_format($value / self::VALUE_MB, 4) . " MB";
        } else {
            $valueStr = number_format($value / self::VALUE_GB, 4) . "GB";
        }
        return $valueStr;
    }
}