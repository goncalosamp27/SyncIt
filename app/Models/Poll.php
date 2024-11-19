<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Poll extends Model
{
    use HasFactory;

    protected $table = 'poll';

    protected $primaryKey = 'poll_id';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'start_date',
        'end_date',
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'event_id' => 'required|exists:event,event_id',  
            'start_date' => 'required|date|after_or_equal:today',  
            'end_date' => 'required|date|after:start_date',  
        ]);

        return $validator;
    }
}
