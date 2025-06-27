<?php

namespace App\Services\Account;

use App\Models\Pharmacy;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class UserService
{
    public function createAdmin(array $payload): array
    {
        $user = User::create($payload['admin']);

        $payload['pharmacy']['owner_id'] = $user->id;
        $pharmacy = Pharmacy::create($payload['pharmacy']);
        $user->pharmacy_id = $pharmacy->id;
        $user->assignRole('admin');
        $user->save();
        $user->load('pharmacy');
        $data['user'] = $user;

        if ($payload['login']) {
            $data['token'] = $user->createToken('token')->plainTextToken;
        }

        return $data;
    }

    public function createWorker(array $payload)
    {
        DB::beginTransaction();
        try {
            $allow_ticket = Cache::get('signup_ticket:' . $payload['worker']['email']);
            if (!$allow_ticket) {
                //! this is not whitelisted
                throw new AccessDeniedHttpException();
            }
            $payload['worker']['pharmacy_id'] = $allow_ticket;
            $worker = User::create($payload['worker']);
            $worker->assignRole('worker');

            DB::table('pharmacy_staff_whitelist')->insert([
                'pharmacy_id' => $worker->pharmacy_id,
                'email' => $payload['worker']['email']
            ]);

            Cache::delete('signup_ticket:' . $payload['worker']['email']);

            if ($payload['login']) {
                $data['token'] = $worker->createToken('token')->plainTextToken;
            }

            $data['worker'] = $worker;
            DB::commit();
            return $data;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
