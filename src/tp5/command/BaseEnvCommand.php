<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 9:16
 */

namespace toolbox\tp5\command;

use ReflectionClass;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use toolbox\loader\EnvLoader;

/**
 * 使用方式 php think command -E dev -L info
 * @title tp5\command\BaseEnvCommand
 * @description
 * @author: daniel
 */
abstract class BaseEnvCommand extends Command
{
    protected $commandName;
    protected $description;

    protected function configure()
    {
        $this->setName($this->getCommandName())->setDescription($this->getCommandDescription());
        $this->addOption("env", "-E", Option::VALUE_OPTIONAL, "You must define the env", "local");
        $this->addOption("logLevel", "-L", Option::VALUE_OPTIONAL, "You must define the log level to display Level.you can use info error.", "info");
    }


    protected function initialize(Input $input, Output $output)
    {

        parent::initialize($input, $output);
        //
        $currentClassFileName = (new ReflectionClass(get_class($this)))->getFileName();
        $dirName = dirname($currentClassFileName);
        $env = $input->getOption("env");
        $logLevel = $input->getOption("logLevel");
        define("APP_COMMAND_ENV", $env);
        define("COMMAND_LOG_LEVEL", $logLevel);
        // 尝试读取当前子项目的配置、函数以及环境
        EnvLoader::envLoad($dirName . "/../env");
        // 默认加载全局环境，可以手动在子环境中禁用全局环境加载
        if (!defined("LOAD_APP_END")|| LOAD_APP_END !== false) {
            echo "开始加载全局环境 $env\n";
            EnvLoader::envLoad(APP_PATH . "/../env");
        }
        is_file($dirName . "/../config.php") && \think\Config::load($dirName . "/../config.php");
        is_file($dirName . "/../common.php") && require_once $dirName . "/../common.php";
        echo "程序运行环境为 $env\n";
    }

    protected function execute(Input $input, Output $output)
    {
        $this->executeCommand($input, $output);
    }

    /**
     * command主执行入口，你应该把运行代码写到这里
     * @title executeCommand
     * @description
     * @param Input $input
     * @param Output $output
     * @return mixed
     * @author: daniel
     */
    public abstract function executeCommand(Input $input, Output $output);

    /**
     * 初始化command运行名
     * @title getCommandName
     * @description
     * @return mixed
     * @author: daniel
     */
    public abstract function getCommandName();

    /**
     * 返回当前command的描述
     * @title getCommandDescription
     * @description
     * @return mixed
     * @author: daniel
     */
    public abstract function getCommandDescription();

}