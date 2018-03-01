<?php
require "../../../vendor/autoload.php";
// 加载框架基础文件
$dirName=realpath(__DIR__ . "/loader/");
\toolbox\loader\TestLoader::tp5ModuleLoader($dirName);

