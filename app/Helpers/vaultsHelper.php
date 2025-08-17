<?php

namespace App\Helpers;

use App\Models\User;

class vaultsHelper
{
    public static function getMain(User $user)
    {
        return $user->pharmacy->vaults()->where('name', '=', 'main')->firstOrFail();
    }

    public static function getCharity(User $user) 
    {
        return  $user->pharmacy->vaults()->where('name', '=', 'charity')->firstOrFail();
    }
}
