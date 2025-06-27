<?php

namespace App\Http\Controllers;

use App\Mail\EmailInvitation;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    use ApiResponse;

    public function sendInvitation(Request $request)
    {
        DB::beginTransaction();
        $request->validate([
            'email' => ['required', 'email'],
            'first_name' => ['required', 'string']
        ]);

        $pharmacyId = $request->user()->pharmacy_id;

        $exists = DB::table('pharmacy_staff_whitelist')
            ->where('pharmacy_id', $pharmacyId)
            ->where('email', $request->email)
            ->exists();

        if ($exists) {
            return ApiResponse::fail('This email is already invited.', [], 419);
        }

        try {
            //? WHEN ACCEPTING ONLY
            // DB::table('pharmacy_staff_whitelist')->insert([
            //     'pharmacy_id' => $pharmacyId,
            //     'email' => $request->email,
            // ]);
            Cache::put('signup_ticket:'.$request->email,$pharmacyId,now()->addDays(7));
            Mail::to($request->email)->queue(new EmailInvitation(
                $request->first_name
            ));
            DB::commit();
            return ApiResponse::success('Invitation email was sent successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
