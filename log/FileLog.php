<?php
namespace toolbox\log;
/**
 *
 */
class FileLog extends BaseLog
{
    private $file;

    function __construct($logPath)
    {
        $this->file = fopen($logPath, "a+");
    }

    function changeLogFile($path)
    {
        fclose($this->file);
        $this->file = fopen($path, "a+");
    }

    public function doLog($msg = '')
    {
        $dateS = date("Y-m-d H:i:s");
        $toWrite = "$dateS\t$msg\n";
        fwrite($this->file, $toWrite);
    }

    public function getNextLine()
    {
        return "\n";
    }
}