<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens'; 
    protected $primaryKey = 'id'; 
    public $timestamps = false;

    protected $fillable = [
        'member_id',
        'token',
        'created_at',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public static function createToken($memberId)
    {
        // Delete old tokens for this user
        self::where('member_id', $memberId)->delete();

        $token = \Illuminate\Support\Str::random(60);

        self::create([
            'member_id' => $memberId,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        return $token;
    }

    public static function isValidToken($memberId, $token)
    {
        $record = self::where('member_id', $memberId)->where('token', $token)->first();

        return $record && Carbon::parse($record->created_at)->diffInMinutes(Carbon::now()) <= 60; // Valid for 60 minutes
    }

    public static function clearTokens($memberId)
    {
        self::where('member_id', $memberId)->delete();
    }
}
