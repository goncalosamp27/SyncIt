<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Invitation extends Model
{
    use HasFactory;

    protected $table = 'invitation';

    protected $primaryKey = 'invitation_id';

    public $timestamps = false;

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'invitation_message' => 'nullable|string|max:500',  
            'invitation_date' => 'required|date|after_or_equal:today',  
            'event_id' => 'required|exists:event,event_id',  
            'invitor_id' => 'required|exists:member,member_id',
            'member_id' => 'required|exists:member,member_id',  
        ]);

        return $validator;
    }

    public function event() 
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function invitationNotifications() 
    {
        return $this->hasMany(InvitationNotification::class, 'invitation_id', 'invitation_id');
    }

    public function invitor() {
        return $this->belongsTo(Member::class, 'invitor_id', 'member_id');
    }
}
