<?php
namespace Min\StorageClient;
use Illuminate\Filesystem\FilesystemAdapter;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\PathPrefixer;

class StorageSystemAdapter extends FilesystemAdapter
{
    public function __construct(FilesystemOperator $driver, StorageDriveAdapter $adapter, array $config = [])
    {
        $this->driver = $driver;
        $this->adapter = $adapter;
        $this->config = $config;
        $separator = $config['directory_separator'] ?? DIRECTORY_SEPARATOR;

        $this->prefixer = new PathPrefixer($config['root'] ?? '', $separator);

        if (isset($config['prefix'])) {
            $this->prefixer = new PathPrefixer($this->prefixer->prefixPath($config['prefix']), $separator);
        }
    }
    public function getdata($name)
    {
        $adapter = $this->adapter;
        if (method_exists($adapter, 'getdata')) {
            return $adapter->getdata($name);
        } elseif (method_exists($this->driver, 'getdata')) {
            return $this->driver->getdata($name);
        }
    }
    public function getId($name)
    {
        $adapter = $this->adapter;
        if (method_exists($adapter, 'getId')) {
            return $adapter->getId($name);
        } elseif (method_exists($this->driver, 'getId')) {
            return $this->driver->getId($name);
        }
    }
    public function restore($id)
    {
        $adapter = $this->adapter;
        if (method_exists($adapter, 'restore')) {
            return $adapter->restore($id);
        }
    }
}
