# sabre-dav-aliyun-oss

使用`sabre/dav`对 Aliyun Oss 进行webdav封装。 方便其他程序进行操作

## Example

```php
<?php

use Sabre\DAV;

// The autoloader
require '../vendor/autoload.php';

Go2i\Sabre\AliyunOSS\OssClient::init(new Go2i\Sabre\AliyunOSS\OssClientConfig('', ''));

$rootDirectory = new Go2i\Sabre\AliyunOSS\OssDirectory();
$server = new DAV\Server($rootDirectory);

$lockBackend = new DAV\Locks\Backend\File('../data/locks');
$lockPlugin = new DAV\Locks\Plugin($lockBackend);
$server->addPlugin($lockPlugin);

$server->addPlugin(new DAV\Browser\Plugin());

$server->debugExceptions = true;

$server->exec();


```
