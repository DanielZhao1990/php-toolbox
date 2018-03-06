<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/6
 * Time: 9:28
 */


class EnvLoaderTest extends \toolbox\test\BaseTestCase
{

    /**
     * @description
     * @author: daniel
     * @dataProvider providerGetEnvByHost
     */
    public function testGetEnvByHost($host,$want)
    {
        $_SERVER["HTTP_HOST"]=$host;
        $this->assertEquals($want,\toolbox\loader\EnvLoader::getEnvByHost(\toolbox\loader\EnvLoader::$defaultEnvMap));
    }

    /**
     * @description
     * @author: daniel
     */
    public function providerGetEnvByHost()
    {
        $demoMap = [
            "localhost" => "local",
            "127.0.0.1" => "local",
            //匹配 192.168.0.* ip段
            "192.168.0.73" => "local",
            "192.168.90.73" => "dev",
            "test.51zsqc.com" => "dev",
            "ceshi.51zsqc.com" => "dev",
            "sdedc.51zsqc.com" => "dev",
            "192.168.90.32" => "dev",
            "sdkljflksdjfkl" => "local",
            "51zsqc.com" => "product",
            "192.168.90.22" => "product",
            "192.168.90.24" => "product",
        ];
        $ret=[];
        foreach ($demoMap as $key=>$item) {
            $ret[]=[$key,$item];
        }
        return $ret;
    }
}