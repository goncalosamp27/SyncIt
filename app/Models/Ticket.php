<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'ticket';
    protected $primaryKey = 'ticket_id';
    protected $fillable = [
        'event_id',
        'ticket_date',
        'member_id',
        'owner'
    ];
    public $timestamps = false;

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'event_id' => 'required|exists:event,event_id',  
            'ticket_date' => 'required|date|after_or_equal:today',  
            'member_id' => 'required|exists:member,member_id',  
            'owner' => 'required|regex:/^[A-Za-z0-9_. ]+$/|min:3|max:50',
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
