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

    //association 
}
