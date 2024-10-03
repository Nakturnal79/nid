<?php

namespace Ekeng\Nid;

use Illuminate\Support\ServiceProvider;

class NidServiceProvider extends ServiceProvider

{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations')
        ], 'migrations');
    }
    public function register()
    {
        //
    }
}
