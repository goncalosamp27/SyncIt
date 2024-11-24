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

}
