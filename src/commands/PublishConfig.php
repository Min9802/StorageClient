<?php

namespace Min\StorageClient\Commands;

use Illuminate\Console\Command;
use Min\StorageClient\Providers\StorageProvider;

class PublishConfig extends Command
{
    protected $signature = 'publish:config';

    protected $description = 'Publish the configuration file for the StorageProvider package';

    public function handle()
    {
        $provider = new StorageProvider($this->laravel);

        $provider->publishConfig();

        $this->info('Configuration file published successfully!');
    }
}
