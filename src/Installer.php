<?php

namespace Ekeng\Nid;

use Illuminate\Support\Facades\Event;

class Installer
{
    public static function postInstall(Event $event)
    {
        self::publishMigrations();
    }

    public static function postUpdate(Event $event)
    {
        self::publishMigrations();
    }


    private static function publishMigrations()
    {
        if (file_exists(getcwd() . '/artisan')) {
            echo "Running post-install commands...\n";
            exec('php artisan vendor:publish --tag=migrations');
        } else {
            echo "Artisan not found in current directory\n";
        }
    }
}
