<?php

namespace toolbox\system;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016/11/29
 * Time: 11:17
 */
class Environment
{
    /**
     * 0= windows 1=其他
     * @title getSystemType
     * @description
     * @author: daniel
     */
    public static function isWindows()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return 1;
        } else {
            return 0;
        }
    }
}