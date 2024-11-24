<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Member extends Authenticatable
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'member_id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'display_name',
        'email',
        'password',
        'bio',
        'profile_pic_url',
        'member_status',
        'remember_token', 
    ];

    // Constraints
    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'username' => 'required|alpha_num|min:3|max:50',
            'display_name' => 'required|regex:/^[A-Za-z0-9_ ]+$/|min:3|max:50',
            'email' => 'required|email|unique:member,email',
            'password' => 'required|min:8|max:100',
            'member_status' => 'required|in:Active,Suspended,Banned',
            'bio' => 'nullable|regex:/^[A-Za-z0-9_.,?!\s]*$/|max:200',
            'profile_pic_url' => 'nullable|url|max:200',
        ]);

        return $validator;
    }

    public static function createMember($data)
    {
        $validator = self::validate($data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return self::create($data);
    }

    // Email verification
    public static function checkIfEmailExists($email)
    {
        $user = self::where('email', $email)->first();
        return $user !== null;
    }

    // Password verification
    public static function checkCredentials($email, $password)
    {
        $user = self::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            return true;
        }

        return false;
    }

    // Relationships

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'member_id', 'member_id');
    }

    public function restrictions()
    {
        return $this->hasMany(Restriction::class, 'member_id', 'member_id');
    }

    public function event()
    {
        return $this->hasOne(Event::class, 'member_id', 'member_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'member_id', 'member_id');
    }

    public function getOptionsByPollId($pollId)
    {
        return Option::where('poll_id', $pollId)->get();
    }

    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'inviter_id', 'member_id');
    }

    public function followNotifications()
    {
        return $this->hasMany(FollowNotification::class, 'follower_id', 'member_id');
    }
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'member_id', 'member_id');
    }
}
