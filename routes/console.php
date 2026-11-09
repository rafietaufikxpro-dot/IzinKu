<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('kehadiran:deteksi-belum-fingerprint')
    ->dailyAt(config('izin.batas_waktu_fingerprint', '07:00'));
