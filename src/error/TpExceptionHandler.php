<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/12
 * Time: 16:46
 */

namespace toolbox\error;

use think\exception\Handle;


/**
 * Please set  'exception_handle'       => 'toolbox\error\TpExceptionHandler', in config.php.It will print error info.
 *
 * @title toolbox\error\TpExceptionHandler
 * @description
 * @author: daniel
 */
class TpExceptionHandler extends Handle
{

    /**
     * Report or log an exception.
     *
     * @param  \Exception $exception
     * @return void
     */
    public function report(\Exception $exception)
    {
        if (!$this->isIgnoreReport($exception)) {
            $data=\toolbox\error\ExceptionHandler::renderException($exception);
            print_r($data);
        }
    }
}