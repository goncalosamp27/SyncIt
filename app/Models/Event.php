<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    public $timestamps = false;
 
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'event_name',
        'event_date',
        'location',
        'description',
        'refund',
        'price',
        'type_of_event',
        'rating',
        'capacity',
        'event_media',
        'artist_id'
    ];

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
            'capacity' => 'required|numeric|min:10',
            'event_media' => 'required|string|max:100',
            'artist_id' => 'required|string'   
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

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id');
    }   


    // Accessor for ticket count
    public function getTicketCountAttribute()
    {
        return $this->tickets()->count();
    }

    public static function upcomingEvents()
    {
        return self::where('event_date', '>', Carbon::now())->get();
    }
    public static function pastEvents()
    {
        return self::where('event_date', '<', Carbon::now())->get();
    }

    public static function createEvent($data)
    {
        $validator = self::validate($data);

        if ($validator->fails()) {
            return $validator->errors();
        }
        return self::create($data);
    }
}
