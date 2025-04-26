<?php

use App\Mail\DailyRecap;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Mail::to('samokhinteam@gmail.com')->send(new DailyRecap);
})->daily()->at('10:00')->timezone('Asia/Manila');
