<?php
namespace smartQQ;

use GuzzleHttp\Cookie\CookieJar;
use smartQQ\Exception\LoginException;
use smartQQ\Core\Client;

class Login extends Base
{
    protected $certificationUrl;

    /**
     * 客户端id(固定值).
     *
     * @var int
     */
    protected static $clientId = 53999199;

    protected $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    /**
     * 执行登陆
     *
     * @return void
     */
    public function server()
    {
//        $this->makeQrCodeImg();
//        echo "请扫码登陆";
//        while (true) {
//            $status = $this->getQcCodeStatus();
//            if ($status == 4) {
//                echo "\r\n登陆成功";
//                break;
//            } elseif ($status == 2) {
//                $this->makeQrCodeImg();
//                echo "\r\n二维码失效,请重新扫码";
//            }
//            sleep(1);
//            echo '.';
//        }
//
//        $this->init();

        $this->client->message->listen();
    }

    protected function init()
    {
        $ptWebQQ = $this->getPtWebQQ($this->certificationUrl);
        $vfWebQQ = $this->getVfWebQQ($ptWebQQ);
        list($uin, $pSessionId) = $this->getUinAndPSessionId($ptWebQQ);

        //持久化登陆信息
        $this->client->identification->store(
            $ptWebQQ,
            $vfWebQQ,
            $pSessionId,
            $uin,
            self::$clientId,
            $this->client->http->getCookies()
        );
    }

    protected function getPtWebQQ($uri)
    {
        $this->client->http->get($uri);

        foreach ($this->client->http->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'ptwebqq')) {
                return $cookie->getValue();
            }
        }

        throw new LoginException('Can not find parameter [ptwebqq]');
    }

    protected function getVfWebQQ($ptWebQQ)
    {
        $this->setToken('ptwebqq', $ptWebQQ);
        $uri = $this->processUri(self::Get_VfWebQQ);

        $options['headers'] = [
            'Referer' => 'http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1',
        ];

        $response = $this->client->http->get($uri, $options)->getBody();
        $body = json_decode($response, true);

        if (isset($body['result']) && !empty($body['result']['vfwebqq'])) {
            return $body['result']['vfwebqq'];
        }

        throw new LoginException('Can not find parameter [vfwebqq]');
    }

    protected function getUinAndPSessionId($ptWebQQ)
    {
        $params['r'] = json_encode([
            'psessionid' => '',
            'status'     => 'online',
            'ptwebqq'    => $ptWebQQ,
            'clientid'   => static::$clientId,
        ]);

        $options = array(
            'form_params' => $params,
            'headers'     => [ 'Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2']
        );

        $response = $this->client->http->post('http://d1.web2.qq.com/channel/login2', $options)->getBody();
        $body = json_decode($response, true);

        if (isset($body['result']) &&
            !empty($body['result']['uin']) &&
            !empty($body['result']['psessionid'])
        ) {
            return array($body['result']['uin'], $body['result']['psessionid']);
        }

        throw new LoginException('Can not find parameter [uin and psessionid]');
    }

    /**
     * 获取登陆二维码,并将二维码保存到本地
     *
     * @return void
     */
    protected function makeQrCodeImg()
    {
        $this->client->http->setCookies(new CookieJar());
        $response = $this->client->http->get(self::GET_QR_CODE);

        foreach ($this->client->http->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'qrsig')) {
                $qrsig = $cookie->getValue();
                $this->tokens['ptqrtoken'] = static::hash33($qrsig);
            }
        }

        file_put_contents('qrCode.png', $response->getBody());
    }

    /**
     * 获取登陆二维码的状态
     *
     * @return int
     * @throws LoginException
     */
    protected function getQcCodeStatus()
    {
        $uri = $this->processUri(self::GET_QR_CODE_STATUS);
        $text = $this->client->http->get($uri)->getBody();
        switch (true) {
            case (false !== strpos($text, '未失效')):
                return 1;
            case (false !== strpos($text, '已失效')):
                return 2;
            case (false !== strpos($text, '认证中')):
                return 3;
            default:
                //找出认证url
                if (preg_match("#'(http.+)'#U", strval($text), $matches)) {
                    $this->certificationUrl = trim($matches[1]);
                    return 4;
                }
                throw new LoginException('Can not find certification url');
        }
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
     * @param int $index
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

}