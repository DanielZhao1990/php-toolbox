<?php

use toolbox\encode\SimpleEncoder;
use toolbox\system\Environment;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/8
 * Time: 15:37
 */

class FileHelperTest extends \toolbox\test\BaseTestCase
{

    /**
     * @dataProvider providerFileExist
     */
    public function testFileExist($fileName){
        $res = \toolbox\file\FileHelper::file_exists($fileName);
        if($res){
            echo "存在";
        }else{
            echo "不存在";
        }
    }


    public function providerFileExist(){
        return [
            [__DIR__."/test/留下来吧测试文档"],
            [__DIR__."/../net/UrlHelperTest.php"],
            [__DIR__ . "/test/测试乱七八炸"]
        ];

    }

    /**
     * @param string $input
     * @param string $encode
     * @dataProvider providerConversion
     */
    public function testConversion($input="我爱祖国",$convertTo,$convertBack){
            $convert=mb_convert_encoding($input,$convertTo,"UTF-8");
            $detect_encoding=SimpleEncoder::mb_detect_encoding($convert);
            $this->assertEquals($convertBack,$detect_encoding);
            $convert=mb_convert_encoding($input,"UTF-8",$detect_encoding);
            $this->assertEquals("UTF-8",SimpleEncoder::mb_detect_encoding($convert));
    }

    public function providerConversion()
    {
        return [
            [
            " Composer 来安装项目的依赖。如果在当前目录下没有一个",
                "UTF-8",
                "UTF-8",
            ],
            [
    " Composer 来安装项目的依赖。如果在当前目录下没有一个",
                "GBK",
                "CP936",
            ],
            [
    "现在我们将使用 Composer 来安装项目的依赖。如果在当前目录下没有一个",
                "CP936",
                "CP936",
            ],
            [
    "现在我们将使用 Composer 来安装项目的依赖。如果在当前目录下没有一个",
                "GB2312",
                "CP936",
            ],
            [
    "现在我们将使用 Composer 来安装项目的依赖。如果在当前目录下没有一个",
                "EUC-CN",
                "CP936",
            ],

        ];

    }


    public function testConversionTime(){
        $str = "现在我们将使用 Composer 来安装项目的依赖。如果在当前目录下没有一个";
        \toolbox\util\RuntimeCounter::instance("转换");
        $run_counter = new \toolbox\util\RuntimeCounter("转换", 0);
        $run_counter->start();
        if(Environment::isWindows()){
            true;
        }
        $run_counter->log("判断系统耗时");
        mb_detect_encoding($str, array("ASCII", "GBK", 'UTF-8', "GB2312"));
        $run_counter->log("判断字符串类型耗时");
        iconv("UTF-8", "GBK", $str);
        $run_counter->log("转换位GBK耗时");
        iconv("UTF-8", "GB2312", $str);
        $run_counter->log("转换位GB2312耗时");
        $run_counter->end();
    }



}