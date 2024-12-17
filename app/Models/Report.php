<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'report';

    protected $primaryKey = 'report_id';
    
    protected $fillable = [
        'event_id',
        'member_id',
        'message',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function member()
    {
        return $this->belongsTo(Artist::class, 'member_id', 'member_id');
    }
}
