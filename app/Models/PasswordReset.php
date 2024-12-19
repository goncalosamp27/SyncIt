<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordReset extends Model
{
    protected $table = 'password_reset';
    protected $primaryKey = 'email';
    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];

    public static function createToken($email)
    {
        self::where('email', $email)->delete();

        $token = \Illuminate\Support\Str::random(60);

        self::create([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public static function isValidToken($email, $token)
    {
        $record = self::where('email', $email)->where('token', $token)->first();

        return $record && Carbon::parse($record->created_at)->diffInMinutes(Carbon::now()) <= 60;
    }

    public static function clearTokens($email)
    {
        self::where('email', $email)->delete();
    }
}
