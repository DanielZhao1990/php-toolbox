<?php
namespace toolbox\util;
/**
 * Created by PhpStorm.
 * User: daniel
 * Date: 2016-03-07
 * Time: 16:51
 */
class WatchMan
{

    static function getRunInfo($processName)
    {
        $exec = "ps -aux|grep -v 'grep'|grep '$processName'";
        $runInfo = self::execShell($exec);
        $infoArr = explode("\n", $runInfo);
        foreach ($infoArr as $key => $item) {
            if (!$item) {
                unset($infoArr[$key]);
            }
        }
        return $infoArr;
    }

    static function runTask($task)
    {
        $dir = $task["dir"];
        $cmd_start = $task["cmd_start"];
        self::execShell("cd $dir&&$cmd_start");
    }

    static function execShell($cmd)
    {
        return shell_exec($cmd);
    }
}



