<?php

namespace Min\StorageClient\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

use Min\StorageClient\StorageAdapter;
use Min\StorageClient\StorageClient;
use Min\StorageClient\StorageDrive;
use Min\StorageClient\StorageSystemAdapter;

class StorageProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/client.php',
            'client'
        );
        $this->mergeConfigFrom(
            __DIR__.'/../config/storage.php',
            'storage'
        );
    }
    public function boot()
    {
         $this->publishes([
            __DIR__.'/../config/client.php' => config_path('client.php'),
        ], 'config');
         $this->publishes([
            __DIR__.'/../config/storage.php' => config_path('storage.php'),
        ], 'config');
        try {
            Storage::extend('msv', function ($app, $config) {
                $options = [];
                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new StorageClient($config['uri'], $config['clientId'], $config['clientSecret'], $config['scope']);

                $drive = new StorageDrive($client);

                $adapter = new StorageAdapter($client, $drive);

                $driver = new Filesystem($adapter);
                return new StorageSystemAdapter($driver, $adapter);
            });
        } catch (\Exception$e) {
            Log::error('Message :' . $e->getMessage() . '--line: ' . $e->getLine());
        }
    }
}
