<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 11:51
 */

namespace toolbox\tp5\command;
use ReflectionClass;
use think\console\Input;
use think\console\Output;

/**
 *
 *
 * @title tp5\command\BaseWorkerCommand
 * @description
 * @author: daniel
 */
abstract class BaseWorkerCommand extends BaseEnvCommand
{
    protected function configure()
    {
        parent::configure();
        // 用来读取 start等参数
        $this->addArgument("work_option");//workerman 控制参数
        if (!defined('TP_COMMAND'))
        {
            define('TP_COMMAND', true);
        }

    }

    protected function initialize(Input $input, Output $output)
    {
        //用来通知workman，创建pid的方式。需要与tpworkerman配合使用
        if (!defined('TP_COMMAND_FILE'))
        {
            define('TP_COMMAND_FILE', (new ReflectionClass(get_class($this)))->getFileName());
        }
        parent::initialize($input, $output);
    }


}