

https://github.com/owner888/phpspider/blob/master/core/requests.php

## 1. 升级须知
Requests类对外，BaseRequests来自PHPSpider

- 替换所有static
- public static -> public
- self::$ -> $this->
- self:: -> $this->
- return void -> return mixed
- $this->VERSION -> self::VERSION


示例
```
$request=\toolbox\net\Requests::instance("list");
$request->set_proxy("127.0.0.1:8888");
$header = array();
$header ['Content-Type'] = 'application/json;charset=UTF-8';
$header ['X-PPD-TIMESTAMP'] =  $timestamp;
$header ['X-PPD-TIMESTAMP-SIGN'] =  $Sign_request;
$header ['X-PPD-APPID'] =  appID;
$header ['X-PPD-SIGN'] =  $Sign;
if ($accessToken != null)
    $header ['X-PPD-ACCESSTOKEN'] =  $accessToken;
$request->set_headers($header);
$result=$request->post($url,$param);
```




