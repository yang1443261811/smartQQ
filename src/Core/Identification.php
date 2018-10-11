<?php

namespace smartQQ\Core;

use GuzzleHttp\Cookie\CookieJar;

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

    public function __construct($ptWebQQ, $vfWebQQ, $pSessionId, $uin, $clientId, CookieJar $cookies)
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
            'ptWebQQ' => $this->ptWebQQ,
            'vfWebQQ' => $this->vfWebQQ,
            'pSessionId' => $this->pSessionId,
            'uin' => $this->uin,
            'clientId' => $this->clientId,
            'cookies' => $this->cookies->toArray(),
        ];
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }
}