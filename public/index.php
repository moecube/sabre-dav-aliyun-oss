<?php

use Sabre\DAV;

// The autoloader
require '../vendor/autoload.php';

Go2i\Sabre\AliyunOSS\OssClient::init(new Go2i\Sabre\AliyunOSS\OssClientConfig(
   getenv('OSS_ACCESS_ID'),getenv('OSS_ACCESS_KEY'),getenv('OSS_ENDPOINT')));

$rootDirectory = new Go2i\Sabre\AliyunOSS\OssDirectory(
   getenv('OSS_PREFIX') . $_SERVER['PHP_AUTH_USER'] . '/');
$server = new DAV\Server($rootDirectory);

// 认证
//$pdo = new PDO($_ENV['DATABASE']);
//$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//$authBackend = new Auth\Backend\PDO($pdo);
//$authBackend->setRealm('MyCard');
//$authPlugin = new Auth\Plugin($authBackend);
//$server->addPlugin($authPlugin);

$lockBackend = new DAV\Locks\Backend\File('../data/locks');
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

$server->addPlugin(new DAV\Browser\Plugin());

$server->debugExceptions = true;

$server->exec();
