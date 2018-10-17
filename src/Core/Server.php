<?php

namespace smartQQ\Core;

use smartQQ\Exception\LoginException;

class Server
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
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

        $this->app->message->listen();
    }

    /**
     * 尝试登陆
     *
     * @return bool
     */
    public function tryLogin()
    {
        if (!is_file($this->app->config['credential_file'])) {
            return false;
        }

        $config = json_decode(file_get_contents($this->app->config['credential_file']), true);

        foreach ($config as $key => $val) {
            $this->app->config[$key] = $val;
        }

        $response = $this->app->message->pollMessage();

        if (!$response ||
            strpos($response['retmsg'], 'login error') !== false) {
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
        $response = $this->app->http->get('https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=0&l=M&s=5&d=72&v=4&t=0.1');

        foreach ($this->app->http->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'qrsig')) {
                $qrsig = $cookie->getValue();
                $this->app->config['ptqrtoken'] = static::hash33($qrsig);
            }
        }

        file_put_contents('qrCode.png', $response);
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
        $this->getPtWebQQ();
        $this->getVfWebQQ();
        $this->getUinAndPSessionId();

        //持久化登陆信息
        $cookieJar = $this->app->http->getCookies();
        $credential = $this->app->config->getMany(['ptwebqq', 'vfwebqq', 'psessionid', 'uin', '53999199']);
        $credential = array_merge($credential, [
            'cookies'  => $cookieJar->toArray()
        ]);

        file_put_contents($this->app->config['credential_file'], json_encode($credential));
    }

    /**
     * 获取鉴权字段ptwebqq
     *
     * @return void
     * @throws LoginException
     */
    protected function getPtWebQQ()
    {
        $this->app->http->get($this->app->config['certificationUrl']);

        foreach ($this->app->http->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'ptwebqq')) {
                $this->app->config['ptwebqq'] = $cookie->getValue();
                return;
            }
        }

        throw new LoginException('Can not find parameter [ptwebqq]');
    }

    /**
     * 获取鉴权字段vfwebqq
     *
     * @return void
     * @throws LoginException
     */
    protected function getVfWebQQ()
    {
        $url = sprintf("http://s.web2.qq.com/api/getvfwebqq?ptwebqq=%s&clientid=53999199&psessionid=&t=0.1", $this->app->config['ptwebqq']);
        $response = $this->app->http->get($url, [
            'headers' => ['Referer' => 'http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1']
        ]);

        $body = json_decode($response, true);

        if (empty($body['result']['vfwebqq'])) {
            throw new LoginException('Can not find parameter [vfwebqq]');
        }

        $this->app->config['vfwebqq'] = $body['result']['vfwebqq'];
    }

    /**
     * 获取鉴权字段uin和psessionid
     *
     * @return void
     * @throws LoginException
     */
    protected function getUinAndPSessionId()
    {
        $params['r'] = json_encode([
            'psessionid' => '',
            'status'     => 'online',
            'ptwebqq'    => $this->app->config['ptwebqq'],
            'clientid'   => $this->app->config['clientid'],
        ]);

        $body = $this->app->http->post('http://d1.web2.qq.com/channel/login2', [
            'form_params' => $params,
            'headers'     => ['Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2']
        ]);

        if (empty($body['result']['uin']) || empty($body['result']['psessionid'])) {
            throw new LoginException('Can not find parameter [uin and psessionid]');
        }

        $this->app->config['uin'] = $body['result']['uin'];
        $this->app->config['psessionid'] = $body['result']['psessionid'];

    }

    /**
     * 获取登陆二维码的状态
     *
     * @return int
     * @throws LoginException
     */
    protected function getQcCodeStatus()
    {
        $uri = "https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={$this->app->config['ptqrtoken']}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0";
        $text = $this->app->http->get($uri);
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
                    $this->app->config['certificationUrl'] = trim($matches[1]);
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