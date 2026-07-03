<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTag extends Model
{
    use HasFactory;

    protected $table = 'event_tag';

    protected $primaryKey = null;
    public $incrementing = false;
    //by default primary key will be incremented

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'tag_id'
    ];
    public static function createEventTag(int $eventId, int $tagId)
    {
        $eventTag = new self();

        $eventTag->event_id = $eventId;
        $eventTag->tag_id = $tagId;

        $eventTag->save();
    }

    // Function to get events containing exactly the given tag_ids
    public static function getEventIdsByTags(array $tagIds)
    {
        // Ensure that we only proceed if the array is not empty
        if (empty($tagIds)) {
            return [];
        }

        // Use Eloquent to query the events related to the given tags
        return self::whereIn('tag_id', $tagIds)
            ->get() // Get all matching rows
            ->groupBy('event_id') // Group by event_id to get distinct events
            ->filter(function ($group) use ($tagIds) {
                // Only keep groups where the count of distinct tags matches the input tags count
                return $group->pluck('tag_id')->unique()->count() === count($tagIds);
            })
            ->keys() // Get the event_ids (grouped keys)
            ->toArray(); // Convert to array of event IDs
    }

    public static function getTagsByEventId($eventId)
    {
        return self::where('event_id', $eventId)
            ->pluck('tag_id');  // This retrieves an array of tag_ids associated with the event_id
    }

    

}
