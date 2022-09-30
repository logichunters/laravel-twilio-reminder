<?php

namespace App\Console\Commands;

use App\Http\Controllers\ReminderController;
use Illuminate\Console\Command;

class FirstSendReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder(s) via SMS using Twilio.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $reminder = new ReminderController();
        $reminder->index();
        $this->info('Success! Check your messages.');
    }
}
