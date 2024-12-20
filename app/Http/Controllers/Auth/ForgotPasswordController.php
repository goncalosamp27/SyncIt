<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordResetToken;

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

        $isValid = PasswordResetToken::isValidToken($request->email, $request->token);

        if (!$isValid) {
            return response()->json(['message' => 'This token is invalid or has expired.'], 400);
        }

        $member = Member::where('email', $request->email)->first();

        if (!$member) {
            return response()->json(['message' => 'No member found with this email address.'], 404);
        }

        if (Hash::check($request->password, $member->password)) {
            return response()->json(['message' => 'Your new password cannot be the same as your old password. Please choose a new one.'], 400);
        }

        $member->password = Hash::make($request->password);
        $member->save();

        PasswordResetToken::clearTokens($request->email);

        return response()->json(['message' => 'Password successfully updated.']);

    }

}
