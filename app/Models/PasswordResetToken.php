<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;


class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';

    protected $primaryKey = 'email';

    public $timestamps = false;

    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];


    public static function createToken(string $email): string
    {
        self::where('email', $email)->delete();

        $token = Str::random(60);

        self::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(), 
        ]);

        return $token;
    }



    public static function isValidToken(string $email, string $token): bool
    {
        $record = self::where('email', $email)->first();

        if (!$record) {
            Log::warning('Token validation failed: Record not found.', [
                'email' => $email,
            ]);
            return false;
        }

        if (!Hash::check($token, $record->token)) {
            Log::warning('Token validation failed: Token does not match.', [
                'email' => $email,
                'token' => $token,
            ]);
            return false;
        }

        $createdAt = Carbon::parse($record->created_at, 'UTC');

        $expiresAt = $createdAt->addMinutes(60);

        Log::info('Token Validation Debug', [
            'email' => $email,
            'created_at' => $record->created_at,
            'current_time_utc' => Carbon::now('UTC')->toDateTimeString(),
            'expires_at' => $expiresAt->toDateTimeString(),
            'is_token_valid' => Carbon::now('UTC')->lessThanOrEqualTo($expiresAt),
        ]);

        return Carbon::now('UTC')->lessThanOrEqualTo($expiresAt);
    }


    public static function clearTokens(string $email): void
    {
        self::where('email', $email)->delete();
    }
}
