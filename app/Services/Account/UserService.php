<?php

namespace App\Services\Account;

use App\Models\Pharmacy;
use App\Models\User;

class UserService
{
    public function createAdmin(array $payload) :array
    {
        $user = User::create($payload['admin']);

        $payload['pharmacy']['owner_id'] = $user->id;
        $pharmacy = Pharmacy::create($payload['pharmacy']);
        $user->pharmacy_id = $pharmacy->id;
        $user->assignRole('admin');
        $user->save();
        $user->load('pharmacy');
        $data['user'] = $user;

        if($payload['login'])
        {
            $data['token'] = $user->createToken('token')->plainTextToken;
        }

        return $data;
    }

    public function createWorker(array $payload, User $admin)
    {
        $payload['pharmacy_id'] = $admin['pharmacy_id'];
        $worker = User::create($payload);
        $worker->assignRole('worker');
        $data['worker'] = $worker;
        return $data;
    }
}
