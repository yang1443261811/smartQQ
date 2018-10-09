<?php
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\CookieJar;
use smartQQ\Console\QrCode;

//header("Content-type: image/jpeg");
require '../vendor/autoload.php';

class Client
{
    protected $http;

    protected $cookies;

    protected $qrsig;

    protected $api_map = [
        'showQrCode' => 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=0&l=M&s=5&d=72&v=4&t=0.1',
        'getQrCodeStatus' => 'https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken=%s&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0'
    ];

    public function __construct()
    {
    }

    public function setHttpClient($http)
    {
        $this->http = $http;

        return $this;
    }

    public function showQrCode()
    {
        $this->cookies = new CookieJar();
        $response = $this->makeRequest('get', $this->api_map['showQrCode']);

        foreach ($this->cookies as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'qrsig')) {
                $this->qrsig = $cookie->getValue();
            }
        }

//        echo $response->getBody();
    }

    public function getQcCodeStatus()
    {
        $ptQrToken = static::hash33($this->qrsig);
        $uri = "https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={$ptQrToken}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0";

        $response = $this->makeRequest('get', $uri);

        echo $response->getBody();
    }

    public function makeRequest($method, $uri, $options = array())
    {
        $options = array_merge([
            'verify' => false,
            'cookies'=> $this->cookies,
        ], $options);

        return $this->http->request($method, $uri, $options);
    }

    /**
     * 生成ptqrtoken的哈希函数.
     *
     * @param string $string
     *
     * @return int
     */
    public static function hash33($string)
    {
        $e = 0;
        $n = strlen($string);
        for ($i = 0; $n > $i; ++$i) {
            //64位php才进行32位转换
            if (PHP_INT_MAX > 2147483647) {
                $e = static::toUint32val($e);
            }
            $e += ($e << 5) + static::charCodeAt($string, $i);
        }

        return 2147483647 & $e;
    }

    /**
     * 计算字符的unicode，类似js中charCodeAt
     * [Link](http://www.phpjiayuan.com/90/225.html).
     *
     * @param string $str
     * @param int    $index
     *
     * @return null|number
     */
    public static function charCodeAt($str, $index)
    {
        $char = mb_substr($str, $index, 1, 'UTF-8');
        if (mb_check_encoding($char, 'UTF-8')) {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');

            return hexdec(bin2hex($ret));
        } else {
            return null;
        }
    }


    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    public function getCookie()
    {
        return $this->cookie;
    }
}


$client = new Client();
$client->setHttpClient(new HttpClient);

$client->showQrCode();
$client->getQcCodeStatus();
