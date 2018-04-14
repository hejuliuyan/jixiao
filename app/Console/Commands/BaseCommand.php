<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App;

class BaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    public function execShellWithPrettyPrint($command)
    {
        $this->info('---');
        $this->info($command);
        $output = shell_exec($command);
        $this->info($output);
        $this->info('---');
    }

    public function productionCheckHint($message = '')
    {
        $message = $message ?: 'This is a "very dangerous" operation';
        if (App::environment('production')
            && !$this->option('force')
            && !$this->confirm('Your are in「Production」environment, '.$message.'! Are you sure you want to do this? [y|N]')
        ) {
            exit('Command termination');
        }
    }
}