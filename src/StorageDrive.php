<?php
namespace Min\StorageClient;

class StorageDrive
{
    protected $client;
    protected $option;
    public function __construct(StorageClient $client, $option = [])
    {
        $this->client = $client;
        $this->option = $option;
    }
    public function fileExists($path)
    {
        return true;
    }
    public function getId($path)
    {
        $name = explode('/', $path)[1];
        return $this->client->post( $name, "file", "get" , [
            'name' => $name,
        ]);
    }
    public function getFile($path)
    {
        $name = explode('/', $path)[1];
        return $this->client->post( $name, "file", "get" , [
            'name' => $name,
        ]);
    }
    public function restoreFile($id)
    {
        $this->client->get("trash", "restore", $id);
    }
    public function forceDeleteFile($id)
    {
        $this->client->delete("file", "forcedelete", $id);
    }
    public function url($path)
    {
        $name = explode('/', $path)[1];
        return $this->client->post( $path, "file", "get" , [
            'name' => $name,
        ]);
    }
    public function read($path)
    {
        $name = explode('/', $path)[1];
        return $this->client->post($path, "file", "get" , [
            'name' => $name,
        ]);
    }
    public function getFileStream(string $path)
    {
        $response = $this->client->get($path, "file", "stream", [
            'stream' => true,
        ]);
        return $response->getBody();
    }

    public function write(string $path, string $contents)
    {
        $response =  $this->client->post($path, "file", "upload", [
            'file' => $contents,
        ]);
        return $response;
    }

    public function writeStream(string $path, $resource)
    {
        $response =  $this->client->post("file", "upload", [
            'path' => $path,
            'file' => $resource,
        ]);
        return $response;
    }

    public function update(string $path, string $contents): void
    {
        $this->client->put($path, "file", "update", [
            'body' => $contents,
        ]);
    }

    public function updateStream(string $path, $resource): void
    {
        $this->client->put($path, "file", "update", [
            'body' => $resource,
        ]);
    }

    public function deleteFile($id)
    {
        $this->client->delete("file", "delete", $id);
    }

    public function rename(string $path, string $newPath): void
    {
        $this->client->put($path, "file", "rename", [
            'new_path' => $newPath
        ]);
    }

    public function copy(string $path, string $newPath): void
    {
        $this->client->put($path, "file", "copy", [
           'new_path' => $newPath,
        ]);
    }

    public function getVisibility(string $path): string
    {
        $response = $this->client->get($path, "file", "visibility");
        return $response->getBody()->getContents();
    }

    public function setVisibility(string $path, string $visibility): void
    {
        $this->client->put($path, "file", "setVisibility", [
            'visibility' => $visibility,
        ]);
    }

    public function getLastModified(string $path)
    {
        $response = $this->client->get($path,"file", "modified");
        $timestamp = $response->getBody()->getContents();
        return strtotime($timestamp);
    }
    public function getMimeType(string $path): string
    {
        $response = $this->client->get($path, "file", "getmime");
        return $response->getBody()->getContents();
    }

    public function getFileSize(string $path): int
    {
        $response = $this->client->get($path, "file", "getsize");
        return (int) $response->getBody()->getContents();
    }

    public function listContents(string $directory = '', bool $recursive = false): array
    {
        $response = $this->client->get("file", "get", [
            'query' => [
                'recursive' => $recursive,
            ],
        ]);
        return json_decode($response->getBody()->getContents(), true);
    }

    public function createDirectory(string $directory): void
    {
        $this->client->post("folder","create", $directory);
    }

    public function deleteDirectory(string $directory): void
    {
        $this->client->delete("folder", "delete", $directory);
    }

    public function directoryExists(string $directory): bool
    {
        try {
            $this->client->get("folder", "exists", $directory);
            return true;
        } catch (\GuzzleHttp\Exception\ClientException$e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return false;
            }
            throw $e;
        }
    }

    public function move(string $source, string $destination, array $config = []): void
    {
        $this->client->put($source,"file", "move", [
            'new_path' => $destination,
        ]);
    }
}
