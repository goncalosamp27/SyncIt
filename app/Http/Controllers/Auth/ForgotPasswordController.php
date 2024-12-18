<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordReset;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email field
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!Member::where('email', $request->email)->exists()) {
            return back()->withErrors(['email' => 'The provided email address does not exist in our records.']);
        }

        $status = Password::broker('member')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    //Form for Reset Password
    public function showResetPasswordForm()
    {
        return view('auth.reset-password');
    }
    //update password 
    public function submitPasswordForm(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4|confirmed',
            'password_confirmation' => 'required',
            'token' => 'required|string',
        ]);
        /*
        $dataStamp = \DateTime::createFromFormat('d/m/Y H:i', $request->data_stamp);

        if ($dataStamp === false) {
            return response()->json(['message' => 'Invalid date format provided.'], 400);
        }

        $currentDate = new \DateTime();


        $diff = $currentDate->diff($dataStamp);
        $minutesDiff = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
    
        if ($minutesDiff > 60) {
            return response()->json(['message' => 'The data stamp is older than 60 minutes.'], 400);
        }
        */

        $passwordReset = PasswordReset::where('email', $request->email)->first();
        if (!$passwordReset) {

            $passwordReset = PasswordReset::createToken($request->email);

            return response()->json([
                'message' => 'No existing token found. A new token has been generated and sent to your email.'
            ], 200);
        }

        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return response()->json(['message' => 'No member found with this email address.'], 404);
        }

        $member->password =  Hash::make($request->password);
        $member->save();

        PasswordReset::where('token', $request->token)->delete();

        return response()->json(['message' => 'Password successfully updated.']);

    }

}
