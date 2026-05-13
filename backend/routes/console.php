<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Subscription;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ===============================
// AUTO EXPIRE SUBSCRIPTION
// ===============================
Schedule::call(function () {

    Subscription::where('status', 'active')
        ->where('ends_at', '<', now())
        ->update([
            'status' => 'expired',
            'plan' => 'free'
        ]);

})->everyMinute(); // bisa diganti daily()