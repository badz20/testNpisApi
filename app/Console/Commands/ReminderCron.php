<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use \App\Notifications\Reminder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;


class ReminderCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification reminder via email.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $jobs = DB::table('jobs')->get();

        if(count($jobs)>0){
            Notification::route('mail', env('NOTIFICATION_EMAIL'))
                ->notify(new Reminder);
        }
    }
}
