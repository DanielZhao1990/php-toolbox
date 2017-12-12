<?php
namespace toolbox\log;
/**
 *
 */
class CliLog extends BaseLog
{

    public function doLog($msg = '')
    {
        $dateS = date("Y-m-d H:i:s");
        $toWrite = "$dateS\t$msg\n";
        echo $toWrite;
    }

    public function getNextLine()
    {
        return "\n";
    }
}