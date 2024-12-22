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

    public $timestamps = true;

    protected $fillable = [
        'event_id',
        'title',
        'start_date',
        'end_date',
    ];
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];


    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|string|max:255',
            'event_id' => 'required|exists:events,event_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        return $validator;
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }


    public function options()
    {
        return $this->hasMany(Option::class, 'poll_id', 'poll_id');
    }


    public function scopeActive($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
    // Method to get Polls by Event ID
    public static function getPollsByEventId($eventId)
    {
        return self::where('event_id', $eventId)->get(); // Retrieve all polls by event_id
    }

    public function calculateTotalVotes($poll_id)
    {
        return Voting::countTotalVotes($poll_id);
    }
}
