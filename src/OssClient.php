<?php
/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 2016/10/23
 * Time: 下午2:33
 */

namespace Go2i\Sabre\AliyunOSS;

define('BUCKET', getenv('OSS_BUCKET'));

class OssClient
{
    /**
     * @var \OSS\OssClient
     */
    private static $client;

    public static $bucket = BUCKET;

    public static function init(OssClientConfig $config)
    {
        $OSSClient = new \OSS\OssClient($config->access_token, $config->access_secret, $config->endpoint);

        return static::$client = $OSSClient;
    }

    public static function getClient()
    {
        if (static::$client === null) {
            throw new \Exception('You Should Init The Client First.');
        }

        return static::$client;
    }
}