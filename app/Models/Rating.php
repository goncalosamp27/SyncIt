<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $table = 'rating';
    
    protected $fillable = [
        'event_id',
        'member_id',
        'rating'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}