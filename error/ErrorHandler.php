<?php
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2015/12/15
 * Time: 9:18
 */
namespace toolbox\error;
class ErrorHandler {

// $log = new \toolbox\log\FileLog(__DIR__ . "/log.txt");
    public static $log;
    public static function initial(){
        error_reporting(E_ALL);
        set_error_handler('\toolbox\error\ErrorHandler::appError');
        set_exception_handler('\toolbox\error\ErrorHandler::appException');
        self::$log = new \toolbox\log\CliLog();
    }


    /**
     * 定义全局日志方法
     * @param $data
     */
    static  function l($data){
        self::$log->log($data);
    }
    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
   static function appException($e) {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('E' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
        ErrorHandler::l($error);
    }


    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
static function appError($errno, $errstr, $errfile, $errline) {
        switch ($errno) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                ob_end_clean();
                $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
                ErrorHandler::l($errorStr);
                //            if(C('LOG_RECORD')) Log::write("[$errno] ".$errorStr,Log::ERR);
                //            self::halt($errorStr);
                break;
            default:
                $errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
                global $log;
                ErrorHandler::l($errorStr);
                //            self::trace($errorStr,'','NOTIC');
                break;
        }
    }

} 