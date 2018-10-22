<?php

namespace smartQQ\Core;

class Credential
{
    protected $app;

    /**
     * Identification constructor.
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 获取身份认证信息并解码
     *
     * @return array
     */
    public function get()
    {
        $data = json_decode(file_get_contents($this->app->config['credential_file']), true);

        return $data;
    }

    /**
     * 保存身份认证信息
     *
     * @param array $data
     * @return void;
     */
    public function store(array $data)
    {
        file_put_contents($this->app->config['credential_file'], json_encode($data));
    }
}