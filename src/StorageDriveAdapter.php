<?php
namespace Min\StorageClient;

use League\Flysystem\FilesystemAdapter;

interface StorageDriveAdapter
{
    public function restore($id);
    public function getdata($name);
    public function forceDelete($id);
}
