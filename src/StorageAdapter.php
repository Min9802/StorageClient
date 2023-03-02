<?php
namespace Min\StorageClient;

use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;

class StorageAdapter  implements FilesystemAdapter, StorageDriveAdapter
{
    protected $client;
    protected $drive;

    public function __construct(StorageClient $client, StorageDrive $drive, $option = [])
    {
        $this->client = $client;
        $this->drive = $drive;
    }
    public function getMetaData($path, $option): FileAttributes
    {
        switch ($option) {
            case 'fileSize':
                $fileSize = $this->drive->getFileSize($path);
                return new FileAttributes($path, $fileSize);
                break;
            case 'visibility':
                $visibility = $this->drive->getVisibility($path);
                return new FileAttributes($path, null, $visibility);
                break;
            case 'lastModified':
                $lastModified = $this->drive->getLastModified($path);
                return new FileAttributes($path, null, null, $lastModified);
                break;
            case 'mimeType':
                $mimeType = $this->drive->getMimeType($path);
                return new FileAttributes($path, null, null, null, $mimeType);
                break;
            default:
                $fileSize = $this->drive->getFileSize($path);
                $visibility = $this->drive->getVisibility($path);
                $lastModified = $this->drive->getLastModified($path);
                $mimeType = $this->drive->getMimeType($path);
                return new FileAttributes($path, $fileSize, $visibility, $lastModified, $mimeType);
                break;
        }

    }
    public function getUrl($path)
    {
        $response = $this->drive->url($path);
        $content = $response['content'];
        $url = $content["url"];
        return $url;
    }
    public function getId($name)
    {
        $response = $this->drive->getId($name);
        $content = $response['content'];
        $id = $content["id"];
        return $id;
    }
    public function getdata($name)
    {
        $response = $this->drive->getFile($name);
        $content = $response['content'];
        return $content;
    }
    public function restore($id)
    {
        $this->drive->restoreFile($id);
    }
    public function forceDelete($id)
    {
        $this->drive->forceDeleteFile($id);
    }
    public function fileExists(string $path): bool
    {
        return $this->drive->fileExists($path);
    }

    public function directoryExists(string $directory): bool
    {
        return $this->drive->directoryExists($directory);
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $this->drive->write($path, $contents);
    }

    public function writeStream(string $path, $resource, Config $config): void
    {
        $response = $this->drive->writeStream($path, $resource);
    }

    public function update(string $path, string $contents, Config $config): void
    {
        $response = $this->drive->update($path, $contents);
        $this->Response($response);
    }

    public function updateStream(string $path, $resource, Config $config): void
    {
        $response = $this->drive->updateStream($path, $resource);
        $this->Response($response);
    }

    public function read(string $path): string
    {
        $response = $this->drive->read($path);
        $content = $response['content']["id"];
        return $content;
    }

    public function readStream(string $path)
    {
        return $this->drive->getFileStream($path);
    }

    public function rename(string $path, string $newPath): void
    {
        $this->drive->rename($path, $newPath);
    }

    public function copy(string $path, string $newPath, Config $config): void
    {
        $this->drive->copy($path, $newPath);
    }

    public function delete(string $id): void
    {
        $this->drive->deleteFile($id);
    }

    public function deleteDirectory(string $directory): void
    {
        $this->drive->deleteDirectory($directory);
    }

    public function createDirectory(string $directory, Config $config): void
    {
        $this->drive->createDirectory($directory);
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->drive->setVisibility($path, $visibility);
    }

    public function visibility(string $path): FileAttributes
    {
        return $this->getMetaData($path, 'visibility');
    }

    public function mimeType(string $path): FileAttributes
    {
        return $this->getMetaData($path, 'mimeType');
    }

    public function lastModified(string $path): FileAttributes
    {
        return $this->getMetaData($path, 'lastModified');
    }

    public function fileSize(string $path): FileAttributes
    {
        return $this->getMetaData($path, 'fileSize');
    }

    public function listContents(string $directory = '', bool $recursive = false): iterable
    {
        $contents = $this->drive->listContents($directory, $recursive);
        return array_map(function ($file) {
            $attributes = [
                'type' => $file['type'],
                'path' => $file['path'],
                'timestamp' => $file['timestamp'],
                'size' => $file['size'],
            ];

            if ($file['type'] == 'file') {
                $attributes['mimetype'] =

                $this->mimeType($file['path']);
                $attributes['lastModified'] = $this->lastModified($file['path']);
                $attributes['fileSize'] = $this->fileSize($file['path']);
            } else {
                $attributes['contents'] = null;
            }
            return new FileAttributes($file['path'], ...$attributes);

        }, $contents);
    }

    public function move(string $source, string $destination, Config $config): void
    {
        $this->drive->move($source, $destination);
    }
    public function Response($response)
    {
        return $response;
    }

}
