<?php

use toolbox\net\Requests;

function demo1()
{
    Requests::set_header("Referer", "https://pan.baidu.com/mbox/homepage");
    Requests::set_proxies([
        "https" => "127.0.0.1:8888",
        "http" => "127.0.0.1:8888"
    ]);
    Requests::set_header("Host", "pan.baidu.com");
    Requests::set_header("User-Agent", "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36");
    Requests::set_header("Cookie", "BIDUPSID=B9DD9E91F6054D1F56F13711FE2BD33C; PSTM=1492874148; BAIDUID=343ABD4B764C1AE681B80174B7EF081D:FG=1; PANWEB=1; bdshare_firstime=1495957031783; FP_UID=4e0864ee59b4281184b3751aaa2cead3; panlogin_animate_showed=1; secu=1; MCITY=-%3A; BDUSS=VESktRNWdwZ09DSEtvenliaXpxUzZmZnNVWWZZUzh0V2JXT1Bkem1haWxsaXBhSVFBQUFBJCQAAAAAAAAAAAEAAADSFsMFemhhbzA4Mjl3YW5nAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAKUJA1qlCQNac1; FP_LASTTIME=1510148521121; pan_login_way=1; STOKEN=452d2aa1a548767f076bfaf7acc37fe714b5f258dbaea92b525aecdabe9730e5; SCRC=a57015458c3bce0bde17fb2b1ae9798a; BDCLND=QZo2wsRIIA8mlVFxNVoMSe2UxInwxoRria75WI04%2BqA%3D; BDRCVFR[Zsq6H6L7tfR]=mbxnW11j9Dfmh7GuZR8mvqV; BDRCVFR[S4-dAuiWMmn]=I67x6TjHwwYf0; PSINO=1; H_PS_PSSID=25247_1425_21115_18560_20697_25178_20930; BDORZ=B490B5EBF6F3CD402E515D22BCDA1598; Hm_lvt_7a3960b6f067eb0085b7f96ff5e660b0=1512831711,1513266642,1513357270,1513413035; Hm_lpvt_7a3960b6f067eb0085b7f96ff5e660b0=1513413046; PANPSC=2941488421243882105%3Af4V6dseF2JWpzdb8ezBoWVTcg8wjIh7YUMSmyMqawJ0WEYAQ6I49A4M6La9EbgvREFoFI88SVC2tQVmDvVPqyX4CwX1rF4qv%2Ft%2Fx8bRz7C5KWDBCT3FjnArAtKo7LDDVrK4jRvj5p%2BL76jyG3uDkPYshZ7OchQK1KQDQpg%2B6XCV%2BSJWX9%2F9F%2FHiRThv12%2F1vLvo7HDxacDA%3D");
    self::$initial = true;
}