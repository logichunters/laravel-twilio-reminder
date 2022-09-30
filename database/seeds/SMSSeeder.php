<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Log::info("This seeder is called");
        Log::error("This seeder is called");
    }
}
