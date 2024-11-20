<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Member extends Model
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
    ];

    //constraints
    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'username' => 'required|alpha_num|min:3|max:50',  
            'display_name' => 'required|regex:/^[A-Za-z0-9_ ]+$/|min:3|max:50',  
            'email' => 'required|email|unique:member,email',  
            'password' => 'required|min:8|max:100',  
            'member_status' => 'required|in:Active,Suspended,Banned',  
            'bio' => 'nullable|max:200',
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
    // Relationships

    // 1 Member to many Notifications
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'member_id', 'member_id');
    }

    // 1 Member to many Restrictions
    public function restrictions()
    {
        return $this->hasMany(Restriction::class, 'member_id', 'member_id');
    }

    // 1 Member to 1 Event
    public function event()
    {
        return $this->hasOne(Event::class, 'member_id', 'member_id');
    }

    // 1 Member to many Comments
    public function comments()
    {
        return $this->hasMany(Comment::class, 'member_id', 'member_id');
    }

    // Many Members to 1 Option
    public function options($pollId)
    {
        return Option::where('poll_id', $pollId)->get();
    }

    // 2 Members to many Invitations
    //How to track invited_membres by an invintator with (member_id)
    public function invitations()
    {
        
    }

    // 1 Member to many Follow Notifications
    public function followNotifications()
    {
        return $this->hasMany(FollowNotification::class, 'follower_id', 'member_id');
    }

    // Inheritance for Artist being a Member
    public function artist()
    {
        return $this->hasOne(Artist::class, 'member_id', 'member_id');
    }

}
