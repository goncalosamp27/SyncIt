<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\FileController;
use Illuminate\Notifications\Notifiable;

class Member extends Authenticatable
{
    use HasFactory;

    use Notifiable;
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
        'remember_token',
    ];

    // Constraints
    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'username' => 'required|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'display_name' => 'required|regex:/^[A-Za-z0-9_. ]+$/|min:3|max:50',
            'email' => 'required|email|unique:member,email',
            'password' => 'required|min:8|max:100',
            'bio' => 'nullable|regex:/^[A-Za-z0-9_.,?!\s]*$/|max:200',
            'profile_pic_url' => 'required|string|max:100',
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


    public function deleteAccount($password, $confirmation)
    {
        // Ensure the confirmation message is correct
        if ($confirmation !== 'I want to delete my account') {
            return [
                'status' => false,
                'message' => 'The confirmation message is incorrect.',
            ];
        }

        // Verify the password
        if (!Hash::check($password, $this->password)) {
            return [
                'status' => false,
                'message' => 'The provided password is incorrect.',
            ];
        }

        // Perform account deletion
        try {
            $this->delete();
            return [
                'status' => true,
                'message' => 'Account deleted successfully.',
            ];
        } catch (\Exception $e) {
            dd($e->getMessage());
            return [
                'status' => false,
                'message' => 'Failed to delete the account. Please try again later.',
            ];
        }
    }

    // Relationships

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'member_id');
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
        return $this->hasMany(Invitation::class, 'member_id', 'member_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'member_id', 'member_id');
    }

    public static function isArtist($member_id)
    {
        return Artist::where('artist_id', $member_id)->exists();
    }

    public function getProfileImage() {
        return FileController::get('profile', $this->member_id);
    }    

    public function getMemberStatusAttribute()
    {
        // Find the relevant restriction
        $restriction = $this->restrictions->last(); // You can adjust the logic if needed
        
        // If a restriction exists, determine the status
        if ($restriction) {
            switch ($restriction->type) {
                case 'Suspension':
                    return 'Suspended';
                case 'Ban':
                    return 'Banned';
                default:
                    return 'Active';
            }
        }

        return 'Active';
    }

}

