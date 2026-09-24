<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;
use App\Models\PasswordResetRequest;

Schedule::call(function () {
    PasswordResetRequest::where('is_used', true)
        ->orWhere('expires_at', '<', now())
        ->delete();
})->daily();
