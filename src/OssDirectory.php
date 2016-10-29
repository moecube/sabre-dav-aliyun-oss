<?php

namespace Go2i\Sabre\AliyunOSS;

use OSS\Core\OssException;
use OSS\Model\ObjectInfo;
use OSS\Model\PrefixInfo;
use Sabre\DAV\Collection;
use Sabre\DAV;

/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 2016/10/22
 * Time: 下午11:04
 */
class OssDirectory extends Collection implements DAV\ICollection, DAV\IQuota
{

    private $directoryPath;

    function __construct($directoryPath = '')
    {
        $this->directoryPath = $directoryPath;
    }

    function getChildren()
    {
        $children = array();

        $objectList = OssClient::getClient()->listObjects(OssClient::$bucket, [
            'delimiter' => '/',
            'prefix' => $this->directoryPath,
            'max-keys' => 1000,
            'marker' => '',
        ]);

        $listObjectInfo = $objectList;

        $objectList = $listObjectInfo->getObjectList(); // 文件列表
        $prefixList = $listObjectInfo->getPrefixList(); // 目录列表


        foreach ($objectList as $objectInfo) {
            if ($objectInfo->getKey() === $this->directoryPath) {
                continue;
            }

            $children[] = $this->getChild($objectInfo);
        }

        foreach ($prefixList as $prefixInfo) {

            $children[] = $this->getChild($prefixInfo);
        }


        return $children;
    }

    function getChild($node)
    {
        if (is_string($node)) {
            $path = $this->directoryPath . $node;

            try {
                $meta = OssClient::getClient()->getObjectMeta(OssClient::$bucket, $path);
                return new OssFile($meta);
            } catch (OssException $e) {
                return new static($path . '/');
            }
        }

        if ($node instanceof PrefixInfo) {
            return new static($node->getPrefix());

        } elseif ($node instanceof ObjectInfo) {
            return new OssFile($node);
        }
    }

    function childExists($name)
    {
        return OssClient::getClient()->doesObjectExist(OssClient::$bucket, $name);
    }

    function getName()
    {
        return basename($this->directoryPath);
    }

    /**
     * Creates a new file in the directory
     *
     * Data will either be supplied as a stream resource, or in certain cases
     * as a string. Keep in mind that you may have to support either.
     *
     * After successful creation of the file, you may choose to return the ETag
     * of the new file here.
     *
     * The returned ETag must be surrounded by double-quotes (The quotes should
     * be part of the actual string).
     *
     * If you cannot accurately determine the ETag, you should not return it.
     * If you don't store the file exactly as-is (you're transforming it
     * somehow) you should also not return an ETag.
     *
     * This means that if a subsequent GET to this new file does not exactly
     * return the same contents of what was submitted here, you are strongly
     * recommended to omit the ETag.
     *
     * @param string $name Name of the file
     * @param resource|string $data Initial payload
     * @return null|string
     */
    function createFile($name, $data = null)
    {
        if ($data === null) {
            return null;
        }

        if (is_resource($data)) {
            $temp_file = tempnam(sys_get_temp_dir(), 'upload/');
            stream_copy_to_stream($data, fopen($temp_file, 'w'));

            OssClient::getClient()->uploadFile(OssClient::$bucket, $name, $temp_file);
        }

        if (is_string($data)) {
            OssClient::getClient()->putObject(OssClient::$bucket, $name, $data);
        }

        $object = OssClient::getClient()->getObjectMeta(OssClient::$bucket, $name);
        return $object['etag'];
    }

    /**
     * Creates a new subdirectory
     *
     * @param string $name
     * @return void
     */
    function createDirectory($name)
    {
        OssClient::getClient()->createObjectDir(OssClient::$bucket, $name);
    }

    /**
     * Deleted the current node
     *
     * @return void
     */
    function delete()
    {
        OssClient::getClient()->deleteObject(OssClient::$bucket, $this->directoryPath);
    }

    /**
     * Renames the node
     *
     * @param string $name The new name
     * @return void
     */
    function setName($name)
    {

    }

    /**
     * Returns the last modification time, as a unix timestamp. Return null
     * if the information is not available.
     *
     * @return int|null
     */
    function getLastModified()
    {
        return null;
    }

    /**
     * Returns the quota information
     *
     * This method MUST return an array with 2 values, the first being the total used space,
     * the second the available space (in bytes)
     */
    function getQuotaInfo()
    {
        return [PHP_INT_MAX, PHP_INT_MAX];
    }
}