<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/10/27
 * Time: 10:17
 */

namespace toolbox\Gps;

/**
 * WGS-84：是国际标准，GPS坐标（Google Earth使用、或者GPS模块）
 * GCJ-02：中国坐标偏移标准，Google Map、高德、腾讯使用
 * BD-09：百度坐标偏移标准，Baidu Map使用84
 *http://www.oschina.net/code/snippet_260395_39205
 * @package Gps\Model
 */
class GpsConverterModel
{
    static $PI = 3.14159265358979324;
    static $x_pi = 0;
    static $instance;
    public static function getInstance()
    {
        if (!self::$instance)
        {
            self::$instance=new GpsConverterModel();
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::$x_pi = 3.14159265358979324 * 3000.0 / 180.0;
    }

    public static function wgs2bd($item)
    {
        $lat = $item["lat"];
        $lng = $item["lng"];
        $temp = self::wgs2gcj($lat, $lng);
        $temp = self::gcj2bd($temp["lat"], $temp["lon"]);
        $item["lat"] = $temp["lat"];
        $item["lng"] = $temp["lon"];
        return $item;
    }
    public static function bd2wgs($item)
    {
        $lat = $item["lat"];
        $lng = $item["lng"];
        $temp = self::bd2gcj($lat, $lng);
        $temp = self::gcj2wgs($temp["lat"], $temp["lon"]);
        $item["lat"] = $temp["lat"];
        $item["lng"] = $temp["lon"];
        return $item;
    }
    //GCJ-02 to BD-09
    public static function gcj2bd($gcjLat, $gcjLon)
    {
        $x = $gcjLon;
        $y = $gcjLat;
        $z = sqrt($x * $x + $y * $y) + 0.00002 * sin($y * self::$x_pi);
        $theta = atan2($y, $x) + 0.000003 * cos($x * self::$x_pi);
        $bdLon = $z * cos($theta) + 0.0065;
        $bdLat = $z * sin($theta) + 0.006;
        return array('lat' => $bdLat, 'lng' => $bdLon);
    }

    //BD-09 to GCJ-02
    public static function bd2gcj($bdLat, $bdLon)
    {
        $x = $bdLon - 0.0065;
        $y = $bdLat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * self::$x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * self::$x_pi);
        $gcjLon = $z * cos($theta);
        $gcjLat = $z * sin($theta);
        return array('lat' => $gcjLat, 'lng' => $gcjLon);
    }
    //WGS-84 to GCJ-02
    public static function wgs2gcj($wgsLat, $wgsLon)
    {
        if (self::outOfChina($wgsLat, $wgsLon))
            return array('lat' => $wgsLat, 'lng' => $wgsLon);

        $d = self::delta($wgsLat, $wgsLon);
        return array('lat' => $wgsLat + $d['lat'], 'lng' => $wgsLon + $d['lng']);
    }

    //GCJ-02 to WGS-84
    public static function gcj2wgs($gcjLat, $gcjLon)
    {
        if (self::outOfChina($gcjLat, $gcjLon))
            return array('lat' => $gcjLat, 'lng' => $gcjLon);

        $d = self::delta($gcjLat, $gcjLon);
        return array('lat' => $gcjLat - $d['lat'], 'lng' => $gcjLon - $d['lng']);
    }

    //GCJ-02 to WGS-84 exactly
    public static function gcj2wgs_exact($gcjLat, $gcjLon)
    {
        $initDelta = 0.01;
        $threshold = 0.000000001;
        $dLat = $initDelta;
        $dLon = $initDelta;
        $mLat = $gcjLat - $dLat;
        $mLon = $gcjLon - $dLon;
        $pLat = $gcjLat + $dLat;
        $pLon = $gcjLon + $dLon;
        $wgsLat = 0;
        $wgsLon = 0;
        $i = 0;
        while (TRUE) {
            $wgsLat = ($mLat + $pLat) / 2;
            $wgsLon = ($mLon + $pLon) / 2;
            $tmp = self::gcj2wgs($wgsLat, $wgsLon);
            $dLat = $tmp['lat'] - $gcjLat;
            $dLon = $tmp['lng'] - $gcjLon;
            if ((abs($dLat) < $threshold) && (abs($dLon) < $threshold))
                break;

            if ($dLat > 0) $pLat = $wgsLat; else $mLat = $wgsLat;
            if ($dLon > 0) $pLon = $wgsLon; else $mLon = $wgsLon;

            if (++$i > 10000) break;
        }
        //console.log(i);
        return array('lat' => $wgsLat, 'lng' => $wgsLon);
    }


    //WGS-84 to Web mercator
    //$mercatorLat -> y $mercatorLon -> x
    public static function mercator_encrypt($wgsLat, $wgsLon)
    {
        $x = $wgsLon * 20037508.34 / 180.;
        $y = log(tan((90. + $wgsLat) * self::$PI / 360.)) / (self::$PI / 180.);
        $y = $y * 20037508.34 / 180.;
        return array('lat' => $y, 'lng' => $x);
        /*
        if ((abs($wgsLon) > 180 || abs($wgsLat) > 90))
            return NULL;
        $x = 6378137.0 * $wgsLon * 0.017453292519943295;
        $a = $wgsLat * 0.017453292519943295;
        $y = 3189068.5 * log((1.0 + sin($a)) / (1.0 - sin($a)));
        return array('lat' => $y, 'lng' => $x);
        //*/
    }
    // Web mercator to WGS-84
    // $mercatorLat -> y $mercatorLon -> x
    public static function mercator_decrypt($mercatorLat, $mercatorLon)
    {
        $x = $mercatorLon / 20037508.34 * 180.;
        $y = $mercatorLat / 20037508.34 * 180.;
        $y = 180 / self::$PI * (2 * atan(exp($y * self::$PI / 180.)) - self::$PI / 2);
        return array('lat' => $y, 'lng' => $x);
        /*
        if (abs($mercatorLon) < 180 && abs($mercatorLat) < 90)
            return NULL;
        if ((abs($mercatorLon) > 20037508.3427892) || (abs($mercatorLat) > 20037508.3427892))
            return NULL;
        $a = $mercatorLon / 6378137.0 * 57.295779513082323;
        $x = $a - (floor((($a + 180.0) / 360.0)) * 360.0);
        $y = (1.5707963267948966 - (2.0 * atan(exp((-1.0 * $mercatorLat) / 6378137.0)))) * 57.295779513082323;
        return array('lat' => $y, 'lng' => $x);
        //*/
    }

    // two point's distance
    public static function distance($latA, $lonA, $latB, $lonB)
    {
        $earthR = 6371000.;
        $x = cos($latA * self::$PI / 180.) * cos($latB * self::$PI / 180.) * cos(($lonA - $lonB) * self::$PI / 180);
        $y = sin($latA * self::$PI / 180.) * sin($latB * self::$PI / 180.);
        $s = $x + $y;
        if ($s > 1) $s = 1;
        if ($s < -1) $s = -1;
        $alpha = acos($s);
        $distance = $alpha * $earthR;
        return $distance;
    }

    private static function delta($lat, $lon)
    {
        // Krasovsky 1940
        //
        // a = 6378245.0, 1/f = 298.3
        // b = a * (1 - f)
        // ee = (a^2 - b^2) / a^2;
        $a = 6378245.0;//  a: 卫星椭球坐标投影到平面地图坐标系的投影因子。
        $ee = 0.00669342162296594323;//  ee: 椭球的偏心率。
        $dLat = self::transformLat($lon - 105.0, $lat - 35.0);
        $dLon = self::transformLon($lon - 105.0, $lat - 35.0);
        $radLat = $lat / 180.0 * self::$PI;
        $magic = sin($radLat);
        $magic = 1 - $ee * $magic * $magic;
        $sqrtMagic = sqrt($magic);
        $dLat = ($dLat * 180.0) / (($a * (1 - $ee)) / ($magic * $sqrtMagic) * self::$PI);
        $dLon = ($dLon * 180.0) / ($a / $sqrtMagic * cos($radLat) * self::$PI);
        return array('lat' => $dLat, 'lng' => $dLon);
    }

    private static function outOfChina($lat, $lon)
    {
        if ($lon < 72.004 || $lon > 137.8347)
            return TRUE;
        if ($lat < 0.8293 || $lat > 55.8271)
            return TRUE;
        return FALSE;
    }

    private static function transformLat($x, $y)
    {
        $ret = -100.0 + 2.0 * $x + 3.0 * $y + 0.2 * $y * $y + 0.1 * $x * $y + 0.2 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::$PI) + 20.0 * sin(2.0 * $x * self::$PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($y * self::$PI) + 40.0 * sin($y / 3.0 * self::$PI)) * 2.0 / 3.0;
        $ret += (160.0 * sin($y / 12.0 * self::$PI) + 320 * sin($y * self::$PI / 30.0)) * 2.0 / 3.0;
        return $ret;
    }

    private static  function transformLon($x, $y)
    {
        $ret = 300.0 + $x + 2.0 * $y + 0.1 * $x * $x + 0.1 * $x * $y + 0.1 * sqrt(abs($x));
        $ret += (20.0 * sin(6.0 * $x * self::$PI) + 20.0 * sin(2.0 * $x * self::$PI)) * 2.0 / 3.0;
        $ret += (20.0 * sin($x * self::$PI) + 40.0 * sin($x / 3.0 * self::$PI)) * 2.0 / 3.0;
        $ret += (150.0 * sin($x / 12.0 * self::$PI) + 300.0 * sin($x / 30.0 * self::$PI)) * 2.0 / 3.0;
        return $ret;
    }


}