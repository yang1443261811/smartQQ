<?php
namespace smartQQ;

use GuzzleHttp\Cookie\CookieJar;
use smartQQ\Exception\LoginException;

class Login extends Base
{
    protected $certificationUrl;

    /**
     * 执行登陆
     *
     * @return void
     */
    public function exec()
    {
        $this->makeQrCodeImg();
        while (true) {
            $status = $this->getQcCodeStatus();
            if ($status == 4) {
                break;
            }
            sleep(1);
        }

        $ptWebQQ = $this->getPtWebQQ($this->certificationUrl);
        $vfWebQQ = $this->getVfWebQQ($ptWebQQ);
    }

    protected function getPtWebQQ($uri)
    {
        $this->http->get($uri);

        foreach ($this->http->getCookies() as $cookie) {
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
        $body = $this->http->get($uri, $options)->getBody();

        if ($body) {
            $body = json_decode($body, true);
            if (isset($body['result']) && !empty($body['result']['vfwebqq'])) {
                return $body['result']['vfwebqq'];
            }
        }

        throw new LoginException('Can not find parameter [vfwebqq]');
    }

    /**
     * 获取登陆二维码,并将二维码保存到本地
     *
     * @return void
     */
    protected function makeQrCodeImg()
    {
        $this->http->setCookies(new CookieJar());
        $response = $this->http->get(self::GET_QR_CODE);

        foreach ($this->http->getCookies() as $cookie) {
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
        $text = $this->http->get($uri)->getBody();
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