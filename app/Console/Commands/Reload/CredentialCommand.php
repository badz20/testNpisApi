<?php

namespace App\Console\Commands\Reload;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CredentialCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reload:credential';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload credentials to default password - StrongPassword1234';

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
        if (app()->environment() != 'production') {
            \App\Models\User::whereNull('email_verified_at')->update([
                'email_verified_at' => date('Y-m-d H:i:s'),
                'password' => Hash::make('StrongPassword1234'),
            ]);
        }

        $this->info('Successfully reload credentials');
    }
}
