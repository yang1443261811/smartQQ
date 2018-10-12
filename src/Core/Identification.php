<?php

namespace smartQQ\Core;

use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;

class Identification
{
    /**
     * 鉴权参数ptwebqq，存储在cookie中.
     *
     * @var string
     */
    protected $ptWebQQ;

    /**
     * 鉴权参数vfwebqq.
     *
     * @var string
     */
    protected $vfWebQQ;

    /**
     * 鉴权参数pSessionId.
     *
     * @var string
     */
    protected $pSessionId;

    /**
     * 客户端id.
     *
     * @var int
     */
    protected $clientId;

    /**
     * 当前登录的用户编号（o+QQ号）.
     *
     * @var string
     */
    protected $uin;

    /**
     * cookie信息,由于client发起请求需要使用cookie信息故cookie也需要一同处理.
     *
     * @var CookieJar
     */
    protected $cookies;

    /**
     * Identification constructor.
     */
    public function __construct()
    {
        if ($this->isExist()) {
            $this->decode();
        }
    }

    /**
     * 身份认证信息是否存在
     *
     * @return bool
     */
    public function isExist()
    {
        return file_exists('./identity.json');
    }

    /**
     * 获取身份认证信息并解码
     *
     * @return array
     */
    protected function decode()
    {
        $data = json_decode(file_get_contents('./identity.json'), true);
        foreach ($data as $name => $value) {
            $this->$name = $value;
        }

        if (isset($data['cookies'])) {
            $this->cookies = new CookieJar();
            foreach ($data['cookies'] as $cookie) {
                $this->cookies->setCookie(new SetCookie($cookie));
            }
        }


    }

    /**
     * 保存身份认证信息
     *
     * @param $ptWebQQ
     * @param $vfWebQQ
     * @param $pSessionId
     * @param $uin
     * @param $clientId
     * @param CookieJar $cookies
     */
    public function store($ptWebQQ, $vfWebQQ, $pSessionId, $uin, $clientId, CookieJar $cookies)
    {
        $this->ptWebQQ = $ptWebQQ;
        $this->vfWebQQ = $vfWebQQ;
        $this->pSessionId = $pSessionId;
        $this->uin = $uin;
        $this->clientId = $clientId;
        $this->cookies = $cookies;

        file_put_contents('./identity.json', json_encode($this->toArray()));
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'ptWebQQ'   => $this->ptWebQQ,
            'vfWebQQ'   => $this->vfWebQQ,
            'pSessionId'=> $this->pSessionId,
            'uin'       => $this->uin,
            'clientId'  => $this->clientId,
            'cookies'   => $this->cookies->toArray(),
        ];
    }

    /**
     * @return string
     */
    public function getPtWebQQ()
    {
        return $this->ptWebQQ;
    }

    /**
     * @return string
     */
    public function getVfWebQQ()
    {
        return $this->vfWebQQ;
    }

    /**
     * @return string
     */
    public function getPSessionId()
    {
        return $this->pSessionId;
    }

    /**
     * @return CookieJar
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * @return int
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @return string
     */
    public function getUin()
    {
        return $this->uin;
    }

}