<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/1/21
 * Time: 11:12
 */

namespace toolbox\common;


class FileSaveHelper
{
    public static function process($pathMap)
    {
        $return = array();
        foreach ($_FILES as $key => $value) {
            $savePath = $pathMap[$key]["path"];
            $typeArr = explode("/", $value['type']);
            if (strtolower($typeArr[0]) !== "image") {
                return;
            }
            $suffix = "." . $typeArr[1];
            $fileName = md5_file($value["tmp_name"]) . $suffix;
            $path = $savePath . $fileName;
            $result = true;
            if (!file_exists($path)) {
                $result = move_uploaded_file($value["tmp_name"], $path);
            }
            if ($result) {
                $return[$key] = $fileName;
            }
        }
        return $return;
    }
}