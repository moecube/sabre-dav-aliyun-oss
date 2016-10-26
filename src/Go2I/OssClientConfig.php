<?php
/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 2016/10/26
 * Time: 下午2:31
 */

namespace Go2i\Sabre\AliyunOSS;


class OssClientConfig
{
    public $access_token;
    public $access_secret;
    public $endpoint;

    public function __construct($access_token, $access_secret, $endpoint = 'oss-cn-shenzhen.aliyuncs.com')
    {
        $this->access_secret = $access_secret;
        $this->access_token = $access_token;
        $this->endpoint = $endpoint;
    }

}