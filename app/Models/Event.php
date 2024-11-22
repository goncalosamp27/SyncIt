<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    public $timestamps = false;

    protected $primaryKey = 'event_id';

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'event_name' => 'required|string|max:100',
            'event_date' => 'required|date|after_or_equal:tomorrow',  
            'location' => 'required|string|max:100',
            'description' => 'required|string',
            'refund' => 'required|numeric|between:0,100',  
            'price' => 'required|numeric|min:0',  
            'type_of_event' => 'required|in:Public,Private',  
            'rating' => 'required|numeric|between:0,5',  
            'artist_id' => 'required|exists:artist,artist_id',  
        ]);

        return $validator;
    }
    //Relationships
    // 1 Event has many Tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id', 'event_id');
    }

    // Many Events belong to 1 Artist
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'artist_id');
    }

    // 1 Event belongs to 1 Member
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id')
                ->withTimestamps(); // This assumes the pivot table has created_at and updated_at timestamps
    }   


    // Accessor for ticket count
    public function getTicketCountAttribute()
    {
        return $this->tickets()->count();
    }

    public static function getAllEvents()
    {
        return self::all();
    }
}
