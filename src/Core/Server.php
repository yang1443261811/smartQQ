<?php

namespace smartQQ\Core;

use smartQQ\Foundation\App;
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

//        $this->app->message->listen();
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

        $this->app->config['server'] = $this->app->credential->get();

        $response = $this->app->message->pollMessage();
        if (!$response || strpos($response['retmsg'], 'login error') !== false) {
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
                $this->app->config['ptqrtoken'] = hash33($qrsig);
            }
        }

        file_put_contents('qrCode.png', $response);
    }

    /**
     * 等待扫码登陆
     *
     * @throws LoginException
     */
    public function waitForLogin()
    {
        echo "请扫码登陆";
        while (true) {
            $uri = "https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={$this->app->config['ptqrtoken']}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0";
            $text = $this->app->http->get($uri);

            switch (true) {
                case (false !== strpos($text, '认证中')):
                case (false !== strpos($text, '未失效')):
                    echo '.';
                    break;
                case (false !== strpos($text, '已失效')):
                    $this->app->log->addInfo('二维码失效,请重新扫码');
                    $this->makeQrCodeImg();
                    break;
                default:
                    //找出认证url
                    if (!preg_match("#'(http.+)'#U", strval($text), $matches)) {
                        throw new LoginException('Can not find certification url');
                    }

                    $this->app->config['certificationUrl'] = trim($matches[1]);
                    $this->app->log->addInfo('二维码认证成功');

                    return;
            }

            sleep(1);
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
        $this->app->credential->store($this->app->config['server']);
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
                $this->app->config['server.ptwebqq'] = $cookie->getValue();
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
        $url = sprintf("http://s.web2.qq.com/api/getvfwebqq?ptwebqq=%s&clientid=53999199&psessionid=&t=0.1", $this->app->config['server.ptwebqq']);
        $options = array('headers' => ['Referer' => 'http://s.web2.qq.com/proxy.html?v=20130916001&callback=1&id=1']);

        $response = $this->app->http->get($url, $options);
        $body = json_decode($response, true);

        if (empty($body['result']['vfwebqq'])) {
            throw new LoginException('Can not find parameter [vfwebqq]');
        }

        $this->app->config['server.vfwebqq'] = $body['result']['vfwebqq'];
    }

    /**
     * 获取鉴权字段uin和psessionid
     *
     * @return void
     * @throws LoginException
     */
    protected function getUinAndPSessionId()
    {
        $options = array('headers' => ['Referer' => 'http://d1.web2.qq.com/proxy.html?v=20151105001&callback=1&id=2']);
        $body = $this->app->http->post('http://d1.web2.qq.com/channel/login2', [
            'psessionid' => '',
            'status' => 'online',
            'ptwebqq' => $this->app->config['server.ptwebqq'],
            'clientid' => $this->app->config['clientid'],
        ], $options);

        if (empty($body['result']['uin']) || empty($body['result']['psessionid'])) {
            throw new LoginException('Can not find parameter [uin and psessionid]');
        }

        $this->app->config['server.uin'] = $body['result']['uin'];
        $this->app->config['server.psessionid'] = $body['result']['psessionid'];
    }
}