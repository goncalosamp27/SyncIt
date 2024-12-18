<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_message',
        'notification_date',
        'member_id',
    ];

    protected $table = 'notification';

    protected $primaryKey = 'notification_id';

    public $timestamps = false;

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'notification_message' => 'required|string',  
            'notification_date' => 'required|date',  
            'member_id' => 'required|exists:member,member_id',  
        ]);
        return $validator;
    }
    public function member(){return $this->belongsTo(Member::class, 'member_id', 'member_id');}
    public function invitationNotification(){return $this->hasOne(InvitationNotification::class, 'notification_id');}
    public function eventNotification(){return $this->hasOne(EventNotification::class, 'notification_id');}
    public function commentNotification(){return $this->hasOne(CommentNotification::class, 'notification_id');}
}
