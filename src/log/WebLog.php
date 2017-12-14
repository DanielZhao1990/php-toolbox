<?php
namespace toolbox\log;
/**
 *
 */
class WebLog extends BaseLog{

    public function doLog($msg = '')
    {
        echo $msg;
    }
}