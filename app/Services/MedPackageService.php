<?php

namespace App\Services;

use App\Models\MedPackage;
use App\Models\User;

class MedPackageService
{
    public function addMedPackages(array $payload, User $user)
    {
        $medPackages = [];
        foreach ($payload['packages-order'] as $package) {
            $package['pharmacy_id'] = $user->pharmacy_id;
            $medPackages[] = MedPackage::create($package);
        }
        return $medPackages;
    }
}
