<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:create {--name=} {--email=} {--password=} {--role=super_admin} {--force}', function () {
    $params = [
        '--name' => $this->option('name'),
        '--email' => $this->option('email'),
        '--password' => $this->option('password'),
        '--role' => $this->option('role'),
    ];
    if ($this->option('force')) {
        $params['--force'] = true;
    }

    return $this->call('make:admin', $params);
})->purpose('Create or update a StudentHub administrator account');
