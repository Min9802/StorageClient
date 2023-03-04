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
    /**
     * folder
     */
    public function listFolder()
    {
        $response = $this->client->get("folder", "list");
        return $response;
    }
    public function createFolder(string $directory)
    {
        $response = $this->client->post("folder", "create", [
            'name' => $directory,
        ]);
        return $response;

    }
    public function renameFolder($directory, $newname)
    {
        $response = $this->client->post("folder", "rename", [
            "name" => $directory,
            'newname' => $newname,
        ]);
        return $response;

    }
    public function getfileFolder($directory)
    {
        $response = $this->client->post("folder", "getfile", [
            'name' => $directory,
        ]);
        return $response;
    }
    public function deleteFolder(string $directory)
    {
        $response = $this->client->post("folder", "delete", [
            'name' => $directory,
        ]);
        return $response;
    }

    public function FolderExists(string $directory)
    {
        $response = $this->client->post("folder", "exists", [
            'name' => $directory,
        ]);
        return $response;
    }

    /**
     * file
     */
    public function renameFile($path, $newname)
    {
        $response = $this->client->post("file", "rename", [
            'path' => $path,
            'newname' => $newname,
        ]);
        return $response;
    }
    public function moveFile($path, $folder)
    {
        $response = $this->client->post("file", "move", [
            'path' => $path,
            'newfolder' => $folder,
        ]);
        return $response;
    }
    /**
     *
     */
    public function uploadFile($folder, $file)
    {

        $response = $this->client->post("file", "upload", [
            'folder' => $folder,
            'file' => $file,
        ]);
        return $response;
    }
    public function updateFile($folder, $file, $id)
    {
        $response = $this->client->post("file", "update", [
            'folder' => $folder,
            'file' => $file,
        ], $id);
        return $response;
    }
    public function fileExists($path)
    {
        return true;
    }
    public function getId($path)
    {
        return $this->client->post("file", "get", [
            'path' => $path,
        ]);
    }
    public function getFile($path)
    {
        return $this->client->post("file", "get", [
            'path' => $path,
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
        return $this->client->post("file", "get", [
            'path' => $path,
        ]);
    }
    public function read($path)
    {
        return $this->client->post("file", "get", [
            'path' => $path,
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
        $response = $this->client->post($path, "file", "upload", [
            'file' => $contents,
        ]);
        return $response;
    }

    public function writeStream(string $path, $resource)
    {
        $response = $this->client->post("file", "upload", [
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
            'new_path' => $newPath,
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
        $response = $this->client->get($path, "file", "modified");
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
    public function move(string $source, string $destination, array $config = []): void
    {
        $this->client->put($source, "file", "move", [
            'new_path' => $destination,
        ]);
    }
}
