<?php

namespace App\Helpers;

class migrationsHelper
{
    public static function load_migrations(): array
    {
        $migrationsPath = database_path('migrations');
        $directories = glob($migrationsPath . '/*', GLOB_ONLYDIR);

        return array_merge([$migrationsPath], $directories);
    }
}
