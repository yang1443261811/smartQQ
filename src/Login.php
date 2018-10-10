<?php
namespace smartQQ;

use GuzzleHttp\Cookie\CookieJar;

class Login extends Base
{
    protected $tokens;

    protected $certificationUrl;

    public function exec()
    {
        $this->makeQrCodeImg();

        while (true) {
            $status = $this->getQcCodeStatus();
            if ($status == 4) {
                echo $this->certificationUrl;
                break;
            }

            usleep(1000);
        }
    }

    protected function makeQrCodeImg()
    {
        $this->Api->setCookies(new CookieJar());

        $response = $this->Api->makeRequest('get', self::GET_QR_CODE);

        foreach ($this->Api->getCookies() as $cookie) {
            if (0 == strcasecmp($cookie->getName(), 'qrsig')) {
                $qrsig = $cookie->getValue();
                $this->tokens['ptqrtoken'] = static::hash33($qrsig);
            }
        }

        $text= $response->getBody();
        file_put_contents('qrCode.png', $text);
    }

    protected function getQcCodeStatus()
    {
        $uri = $this->processUri(self::GET_QR_CODE_STATUS);

        $text = $this->Api->makeRequest('get', $uri)->getBody();

        if (false !== strpos($text, '未失效')) {
            $status = 1;
        } elseif (false !== strpos($text, '已失效')) {
            $status = 2;
        } elseif (false !== strpos($text, '认证中')) {
            $status = 3;
        } else {
            $status = 4;
            //找出认证url
            if (!preg_match("#'(http.+)'#U", strval($text), $matches)) {
                throw new RuntimeException('Can not find certification url');
            }

            $this->certificationUrl = trim($matches[1]);
        }

        return $status;
    }

    public function setToken($name, $value)
    {
        $this->tokens[$name] = $value;
    }

    /**
     * 处理链接中的占位符.
     *
     * @param string $uri
     *
     * @return string
     */
    protected function processUri($uri)
    {
        return preg_replace_callback('#\{([a-zA-Z0-9_,]*)\}#i', function ($matches) {
            return isset($this->tokens[$matches[1]]) ? $this->tokens[$matches[1]] : '';
        }, $uri);
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