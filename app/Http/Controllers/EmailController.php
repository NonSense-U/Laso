<?php

namespace App\Http\Controllers;

use App\Mail\EmailInvitation;
use App\Traits\V1\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    use ApiResponse;
    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => ['required','email'],
            'first_name' => ['required','string']
        ]);

        Mail::to($request->email)->send(new EmailInvitation(
            $request->first_name
        ));

        return ApiResponse::success('Invitation email was sent successfully!');
    }
}
