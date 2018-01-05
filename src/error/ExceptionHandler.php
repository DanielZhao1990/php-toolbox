<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/5
 * Time: 15:24
 */

namespace toolbox\error;


class ExceptionHandler
{
   static public function renderException(\Exception $e)
    {
        $data["code"] = $e->getCode();
        $data["file"] = $e->getFile();
        $data["line"] = $e->getLine();
        $trace = $e->getTrace();
        array_unshift($trace, [
            'function' => '',
            'file' => $e->getFile() !== null ? $e->getFile() : 'n/a',
            'line' => $e->getLine() !== null ? $e->getLine() : 'n/a',
            'args' => [],
        ]);

        for ($i = 0, $count = count($trace); $i < $count; ++$i) {
            $class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
            $type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
            $function = $trace[$i]['function'];
            $file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
            $line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';
            $traces[] = sprintf(' %s%s%s() at <info>%s:%s</info>', $class, $type, $function, $file, $line);
        }
        $data["traces"] = $traces;
        return $data;
    }
}