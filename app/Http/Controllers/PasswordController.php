<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetCodeEmail;
use App\Models\User;
use App\Traits\V1\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class PasswordController extends Controller
{
    use ApiResponse;

    public function sendPasswordResetEmail(Request $request)
    {
        $email = $request->validate([
            'email' => ['required','email','exists:users,email']
        ])['email'];
        
        $user = User::query()->where('email', '=', $email)->firstOrFail();
        $passwordResetcode = rand(10000,1000000);
        Cache::put('forgot-password:' . $email, $passwordResetcode, now()->addMinutes(30));
        Mail::to($user->email)->queue( new PasswordResetCodeEmail($user->first_name, $passwordResetcode));
        return ApiResponse::success('Invitation email was sent successfully!');
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required','email'],
            'code' => ['required']
        ]);

        $actualCode = Cache::get('forgot-password:' . $validated['email']);

        if(empty($actualCode) || $validated['code'] != $actualCode)
        {
            throw new AuthorizationException();
        }

        /** @var \App\Models\User $user  */
        $user = User::query()->where('email', '=', $validated['email'])->firstOrFail();
        $token = $user->createToken('token')->plainTextToken;
        return ApiResponse::success('Success!', ['token' => $token]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'password' => ['required','string','min:8','confirmed']
        ]);

        $user = $request->user();
        $user->password = $validated['password'];
        $user->password_last_changed_at = now();
        $user->save();

        return ApiResponse::success('password has been changed!');
    }
}
