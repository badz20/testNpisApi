<?php

namespace App\Console\Commands\Reload;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload:db
                                {--d|dev : Seed development data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload Database';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->validate();

        $this->prepare();

        $this->dev();

        $this->info('Successfully reload database.');
    }

    private function validate()
    {
        if (app()->environment() == 'production') {
            $this->comment('Nothing need to be done here. Bye.');

            return 0;
        }
    }

    private function prepare()
    {
       
        $this->call('migrate:fresh', [
            '--force' => true,
            '--quiet' => true,
        ]);

        $this->call('storage:link', [
            '--force' => true,
            '--quiet' => true,
        ]);

    }

    private function dev()
    {
        if ($this->option('dev')) {
            $this->call('seed:dev');
        }
    }

}
