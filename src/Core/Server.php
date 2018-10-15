<?php

namespace smartQQ\Core;

use GuzzleHttp\Cookie\CookieJar;
use smartQQ\Exception\LoginException;

class Server
{
    protected $certificationUrl;

    protected $ptqrtoken;

    /**
     * 客户端id(固定值).
     *
     * @var int
     */
    protected static $clientId = 53999199;

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * 执行登陆
     *
     * @return void
     */
    public function server()
    {
        if (!$this->tryLogin()) {
            $this->makeQrCodeImg();
            $this->waitForLogin();
            $this->init();
        }

        $this->client->message->listen();
    }

    /**
     * 尝试登陆
     *
     * @return bool
     */
    public function tryLogin()
    {
        if (!$this->client->credential->isExist()) {
            return false;
        }

        if (!$response = $this->client->message->pollMessage()) {
            return false;
        }

        if (false !== strpos($response['retmsg'], 'login error')) {
            return false;
        }

        return true;
    }

    /**
     * 获取登陆二维码,并将二维码保存到本地
     *
     * @return void
     */
    protected function makeQrCodeImg()
    {
        $this->client->http->setCookies(new CookieJar());
        $response = $this->client->http->get('https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=0&l=M&s=5&d=72&v=4&t=0.1');

        foreach ($this->client->http->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'qrsig')) {
                $qrsig = $cookie->getValue();
                $this->ptqrtoken = static::hash33($qrsig);
            }
        }

        file_put_contents('qrCode.png', $response->getBody());
    }

    /**
     * 等待扫码登陆
     *
     * @return void
     */
    public function waitForLogin()
    {
        echo "请扫码登陆";
        while (true) {
            $status = $this->getQcCodeStatus();
            if ($status == 4) {
                echo "\r\n登陆成功";
                break;
            } elseif ($status == 2) {
                $this->makeQrCodeImg();
                echo "\r\n二维码失效,请重新扫码";
            }
            sleep(1);
            echo '.';
        }
    }

    /**
     * 获取登陆信息并保存
     *
     * @return void
     */
    protected function init()
    {
        $ptWebQQ = $this->getPtWebQQ($this->certificationUrl);
        $vfWebQQ = $this->getVfWebQQ($ptWebQQ);
        list($uin, $pSessionId) = $this->getUinAndPSessionId($ptWebQQ);

        //持久化登陆信息
        $this->client->credential->store(
            $ptWebQQ,
            $vfWebQQ,
            $pSessionId,
            $uin,
            self::$clientId,
            $this->client->http->getCookies()
        );
    }

    /**
     * 获取鉴权字段ptwebqq
     *
     * @param $uri
     * @return mixed
     * @throws LoginException
     */
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

    /**
     * 获取鉴权字段vfwebqq
     *
     * @param $ptWebQQ
     * @return mixed
     * @throws LoginException
     */
    protected function getVfWebQQ($ptWebQQ)
    {
        $uri = "http://s.web2.qq.com/api/getvfwebqq?ptwebqq={$ptWebQQ}&clientid=53999199&psessionid=&t=0.1";

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

    /**
     * 获取鉴权字段uin和psessionid
     *
     * @param $ptWebQQ
     * @return array
     * @throws LoginException
     */
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

        $response = $this->client->http->post('http://d1.web2.qq.com/channel/login2', $options);
        $body = json_decode($response->getBody(), true);

        if (isset($body['result']) &&
            !empty($body['result']['uin']) &&
            !empty($body['result']['psessionid'])
        ) {
            return array($body['result']['uin'], $body['result']['psessionid']);
        }

        throw new LoginException('Can not find parameter [uin and psessionid]');
    }

    /**
     * 获取登陆二维码的状态
     *
     * @return int
     * @throws LoginException
     */
    protected function getQcCodeStatus()
    {
        $uri = "https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={$this->ptqrtoken}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0";
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