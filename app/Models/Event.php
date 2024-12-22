<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth; 

use App\Models\EventNotification;
use App\Http\Controllers\FileController;

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
        'event_status',
        'cancel_date',
        'artist_id',
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
            'event_status' => 'required|in:Active,Cancelled',
            'cancel_date' => 'nullable|date',
            'artist_id' => 'required|string'
        ]);
        return $validator;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id', 'event_id');
    }
    public function invitations()
    {
        return $this->hasMany(Invitation::class, 'event_id', 'event_id');
    }
    public function requests()
    {
        return $this->hasMany(JoinRequest::class, 'event_id', 'event_id');
    }
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'artist_id');
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'event_tag', 'event_id', 'tag_id');
    }
    public function notifications()
    {
        return $this->hasMany(EventNotification::class, 'event_id');
    }
    public function getTicketCountAttribute()
    {
        return $this->tickets()->count();
    }
    public static function upcomingEvents()
    {
        return self::where('event_date', '>', now())
            ->where('event_status', '<>', 'Cancelled')
            ->get();
    }
    public static function pastEvents()
    {
        return self::where('event_date', '<', now())->get();
    }
    public static function createEvent($data)
    {
        $validator = self::validate($data);
        if ($validator->fails()) {
            return $validator->errors();
        }
        return self::create($data);
    }

    //filter events by tags
    public static function getEventsByTagsAndType(array $tagIds, array $eventTypes)
    {
        // Start with a base query
        $query = self::query();

        // Step 1: If tagIds is provided, filter by event tags
        if (!empty($tagIds)) {
            $eventIds = EventTag::getEventIdsByTags($tagIds);
            $query->whereIn('event_id', $eventIds);
        }

        // Step 2: If eventTypes is provided, filter by event type
        if (!empty($eventTypes)) {
            $query->whereIn('type_of_event', $eventTypes);
        }

        // Step 3: Get the filtered events
        return $query->get();
    }

    public function getIsRatedAttribute()
    {
        return $this->ratings()->where('member_id', Auth::id())->exists();
    }

    public function getUserRatingAttribute()
    {
        $rating = $this->ratings()->where('member_id', Auth::id())->first();
        return $rating ? $rating->rating : null;
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'event_id');
    }

    // Method to get average rating for the artist
    public function getArtistAverageRating()
    {
        return $this->hasMany(Rating::class, 'event_id')
            ->join('event', 'rating.event_id', '=', 'event.event_id')
            ->where('event.artist_id', $this->artist_id)
            ->avg('rating');
    }

    public function getEventImage() {
        return FileController::get('event', $this->event_id);
    }

    public static function getTicketCountByEventId($eventId)
{
    $event = self::find($eventId);
    if ($event) {
        return $event->tickets()->count();
    }
    return 0; 
}
    
}