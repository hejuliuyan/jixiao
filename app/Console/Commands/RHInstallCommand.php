<?php

namespace App\Console\Commands;

class RHInstallCommand extends BaseCommand
{
    protected $signature = 'rh:install';

    protected $description = 'Project Initialize Command';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->execShellWithPrettyPrint('php artisan key:generate');
        $this->execShellWithPrettyPrint('php artisan migrate --seed');
    }
}