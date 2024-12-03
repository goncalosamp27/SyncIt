<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class JoinRequest extends Model
{
    use HasFactory;

    protected $table = 'join_request';
    protected $primaryKey = 'request_id';
    public $timestamps = false;

	public static function validate($data)
    {
        $validator = Validator::make($data, [
            'invitation_message' => 'nullable|string|max:500',  
            'invitation_date' => 'required|date|after_or_equal:today',  
            'event_id' => 'required|exists:event,event_id',  
            'member_id' => 'required|exists:member,member_id',  
        ]);

        return $validator;
    }

    public function event() 
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function member()
	{
		return $this->belongsTo(Member::class, 'member_id', 'member_id');
	}
}