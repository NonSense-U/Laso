<?php

namespace App\Services\Account;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AuthService
{

    public function login(array $payload)
    {
        $user = null;

        if (isset($payload['username'])) {
            $user = User::query()->where('username', '=', $payload['username'])->firstOrFail();
        } else if (isset($payload['email'])) {
            $user = User::query()->where('email', '=', $payload['email'])->firstOrFail();
        } else {
            $user = User::query()->where('phone_number', '=', $payload['phone_number'])->firstOrFail();
        }

        if ($user->hasRole('worker')) {
            $is_whitelisted = DB::table('pharmacy_staff_whitelist')->where('email', '=', $user->email)->exists();

            if (!$is_whitelisted) {
                //! UPDATE ERROR
                throw new AccessDeniedHttpException();
            }
        }

        if (!Hash::check($payload['password'], $user['password'])) {
            throw new AuthenticationException("The credintials you provided are not correct.");
        }

        $data['token'] = $user->createToken('token')->plainTextToken;
        return $data;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return;
    }
}
