<?php

namespace App\Console\Commands\Seed;

use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed Unit Test Data';

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
        $this->call('db:seed', ['--class' => '\Database\Seeders\PermissionSeeder', '--quiet' => true]);
        $this->call('db:seed', ['--class' => '\Database\Seeders\SuperUserSeeder', '--quiet' => true]);
        $this->call('passport:install', [
            '--force' => true,
        ]);
    }
}
