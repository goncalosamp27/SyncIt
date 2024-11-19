<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'rating';

    protected $primaryKey = ['event_id', 'member_id'];

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'member_id',
        'rating',
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'event_id' => 'required|exists:event,event_id',     
            'member_id' => 'required|exists:member,member_id',   
            'rating' => 'required|numeric|between:0,5',          
        ]);

        return $validator;
    }
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

   
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    
}
