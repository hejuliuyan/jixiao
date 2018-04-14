<?php

namespace App\Console\Commands;

class RHClearCommand extends BaseCommand
{
    protected $signature = 'rh:clear';

    protected $description = 'Clear and Rebuild all project cache.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        //清理缓存
        $this->execShellWithPrettyPrint('php artisan view:clear');
        $this->execShellWithPrettyPrint('php artisan cache:clear');

        //重建缓存
        $this->execShellWithPrettyPrint('php artisan optimize');
        $this->execShellWithPrettyPrint('php artisan config:cache');
        $this->execShellWithPrettyPrint('php artisan route:cache');
    }
}
