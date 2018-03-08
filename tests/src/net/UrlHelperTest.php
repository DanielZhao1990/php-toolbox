<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/8
 * Time: 15:37
 */

class UrlHelperTest extends \toolbox\test\BaseTestCase
{

    /**
     * @description
     * @author: daniel
     * @dataProvider providerCurrentUrl
     */
    public function testCurrentUrl($server,$want)
    {
        $_SERVER=array_merge($_SERVER,$server);
        $this->assertEquals($want,\toolbox\net\UrlHelper::currentUrl());
    }

    /**
     * @description
     * @author: daniel
     */
    public function providerCurrentUrl()
    {
        return [
            [
                [
                    "SERVER_PORT" => "80",
                    "HTTP_HOST" => "127.0.0.1",
                    "REQUEST_SCHEME" => "http",
                    "REQUEST_URI" => "/weixin/public/index.php/wx/Kanjia/index/activityId/8",
                ],
                "http://127.0.0.1/weixin/public/index.php/wx/Kanjia/index/activityId/8"
            ],
            [
                [
                    "SERVER_PORT" => "8080",
                    "HTTP_HOST" => "127.0.0.1",
                    "REQUEST_SCHEME" => "http",
                    "REQUEST_URI" => "/weixin/public/index.php/wx/Kanjia/index/activityId/8",
                ],
                "http://127.0.0.1:8080/weixin/public/index.php/wx/Kanjia/index/activityId/8"
            ],
            [
                [
                    "SERVER_PORT" => "80",
                    "HTTP_HOST" => "51zsqc.com",
                    "REQUEST_SCHEME" => "http",
                    "REQUEST_URI" => "/weixin/public/index.php/wx/Kanjia/index/activityId/8",
                ],
                "http://51zsqc.com/weixin/public/index.php/wx/Kanjia/index/activityId/8"
            ],
        ];
    }
}