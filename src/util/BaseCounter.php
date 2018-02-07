<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/16
 * Time: 11:20
 */

namespace toolbox\util;


class BaseCounter
{

    protected $logType = 0;
    protected $logFile;
    const LOT_TYPE_CONSOLE = 0;
    const LOT_TYPE_FILE = 1;

    public function __construct()
    {
        if (!defined("APP_PATH")) {
            $this->logFile = __DIR__ . '/log/consumptionTime.txt';
        } else {
            $this->logFile = dirname(APP_PATH) . '/log/consumptionTime.txt';
        }
    }

    /**
     * 直接console输出
     * @description
     * @author: daniel
     * @param $data
     */
    public function printLog($data)
    {
        if ($this->logType == self::LOT_TYPE_CONSOLE) {
            echo $data . "\n";
        } else {
            $this->printTestLog($data);
        }
    }


    /**
     * 输出到日志文件
     * @description
     * @author: daniel
     * @param $data
     */
    public function printTestLog($data)
    {
        $data = print_r($data, true) . "\n";
        file_put_contents($this->logFile, $data, FILE_APPEND);
    }
}