<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PasswordResetToken extends Model
{
    // Specify the table name
    protected $table = 'password_reset_tokens';

    // Define primary key if different
    protected $primaryKey = 'email';

    // Disable timestamps if not used
    public $timestamps = false;

    // Define fillable attributes
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];


    public static function createToken(string $email): string
    {
        // Delete any existing tokens for this email
        self::where('email', $email)->delete();

        // Generate a new token
        $token = Str::random(60);

        // Store the token in the database
        self::create([
            'email' => $email,
            'token' => $token,
            'created_at' => now(), // Use current timestamp
        ]);

        return $token;
    }

    public static function isValidToken(string $email, string $token): bool
    {
        $record = self::where('email', $email)->where('token', $token)->first();

        if (!$record) {
            return false;
        }

        // Check if the token is expired (valid for 60 minutes)
        $createdAt = strtotime($record->created_at);
        $expiresAt = $createdAt + (60 * 60); // 60 minutes

        return time() <= $expiresAt;
    }

    public static function clearTokens(string $email): void
    {
        self::where('email', $email)->delete();
    }
}
