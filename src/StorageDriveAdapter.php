<?php
namespace Min\StorageClient;

use League\Flysystem\FilesystemAdapter;

interface StorageDriveAdapter
{
    public function listFolder();
    public function FolderExists($name);
    public function createFolder($name);
    public function renameFolder($name, $newName);
    public function getfileFolder($name);
    public function deleteFolder($name);

    public function getInfo($name);
    public function uploadFile($folder, $file);
    public function updateFile($folder, $file, $id);
    public function renameFile($path, $newName);
    public function moveFile($path, $newfolder);
    public function restore($id);
    public function forceDelete($id);
}
