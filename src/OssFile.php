<?php
/**
 * Created by PhpStorm.
 * User: leoliang
 * Date: 2016/10/22
 * Time: 下午11:07
 */

namespace Go2i\Sabre\AliyunOSS;


use OSS\Model\ObjectInfo;
use Sabre\DAV\File;
use Sabre\DAV;

class OssFile extends File implements DAV\IFile
{
    private $filePath;
    private $fileName;
    private $fileTimestamp;
    private $fileSize;
    private $fileETag;
    private $fileContentType;

    function __construct($data)
    {
        if (is_array($data)) {
            $meta = $data;

            $this->filePath = explode('/', $meta['_info']['url'], 4)[3];
            $this->fileName = basename($this->filePath);

            $this->fileETag = $meta['etag'];
            $this->fileSize = $meta['content-length'];
            $this->fileTimestamp = strtotime($meta['last-modified']);
            $this->fileContentType = $meta['content-type'];
        }

        if ($data instanceof ObjectInfo) {
            $this->filePath = $data->getKey();
            $this->fileName = basename($data->getKey());

            $this->fileSize = $data->getSize();
            $this->fileETag = $data->getETag();
            $this->fileTimestamp = strtotime($data->getLastModified());
            $this->fileContentType = $data->getType();
        }
    }

    function getName()
    {

        return $this->fileName;

    }

    function get()
    {
        return OssClient::getClient()->getObject(OssClient::$bucket, $this->filePath);

    }

    function getSize()
    {

        return $this->fileSize;

    }

    function getFileETag()
    {

        return $this->fileETag;

    }

    /**
     * Replaces the contents of the file.
     *
     * The data argument is a readable stream resource.
     *
     * After a succesful put operation, you may choose to return an ETag. The
     * etag must always be surrounded by double-quotes. These quotes must
     * appear in the actual string you're returning.
     *
     * Clients may use the ETag from a PUT request to later on make sure that
     * when they update the file, the contents haven't changed in the mean
     * time.
     *
     * If you don't plan to store the file byte-by-byte, and you return a
     * different object on a subsequent GET you are strongly recommended to not
     * return an ETag, and just return null.
     *
     * @param resource|data $data
     * @return string|null
     */
    function put($data)
    {
        if ($data === null) {
            return null;
        }

        if (is_resource($data)) {
            $temp_file = tempnam(sys_get_temp_dir(), 'upload/');
            stream_copy_to_stream($data, fopen($temp_file, 'w'));

            OssClient::getClient()->uploadFile(OssClient::$bucket, $this->filePath, $temp_file);
        }

        if (is_string($data)) {
            OssClient::getClient()->putObject(OssClient::$bucket, $this->filePath, $data);
        }

        $object = OssClient::getClient()->getObjectMeta(OssClient::$bucket, $this->filePath);
        return $object['etag'];
    }

    /**
     * Returns the mime-type for a file
     *
     * If null is returned, we'll assume application/octet-stream
     *
     * @return string|null
     */
    function getContentType()
    {
        return '';
    }

    /**
     * Deleted the current node
     *
     * @return void
     */
    function delete()
    {
        OssClient::getClient()->deleteObject(OssClient::$bucket, $this->filePath);
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
        return $this->fileTimestamp;
    }
}