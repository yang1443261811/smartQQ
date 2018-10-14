<?php
namespace smartQQ;


class Base
{

    protected $tokens;

    const GET_QR_CODE = 'https://ssl.ptlogin2.qq.com/ptqrshow?appid=501004106&e=0&l=M&s=5&d=72&v=4&t=0.1';
    const GET_QR_CODE_STATUS = 'https://ssl.ptlogin2.qq.com/ptqrlogin?ptqrtoken={ptqrtoken}&webqq_type=10&remember_uin=1&login2qq=1&aid=501004106&u1=http%3A%2F%2Fw.qq.com%2Fproxy.html%3Flogin2qq%3D1%26webqq_type%3D10&ptredirect=0&ptlang=2052&daid=164&from_ui=1&pttype=1&dumy=&fp=loginerroralert&action=0-0-4303&mibao_css=m_webqq&t=undefined&g=1&js_type=0&js_ver=10203&login_sig=&pt_randsalt=0';
    const Get_VfWebQQ = 'http://s.web2.qq.com/api/getvfwebqq?ptwebqq={ptwebqq}&clientid=53999199&psessionid=&t=0.1';

    public function __construct()
    {
    }

    protected function setToken($name, $value)
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
}