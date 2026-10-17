<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('chat:ping', function () {
    try {
        $result = Illuminate\Support\Facades\DB::connection('mysql_chat')->select('SELECT 1 as ping');
        $this->info('Chat DB ping successful: ' . now()->toDateTimeString());
        return 0;
    } catch (\Throwable $e) {
        $this->error('Chat DB ping failed: ' . $e->getMessage());
        return 1;
    }
})->purpose('Ping the Aiven Chat Database to keep it active and prevent power off');

