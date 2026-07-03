<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Tag extends Model
{
    use HasFactory;

    protected $table = 'tag';

    protected $primaryKey = 'tag_id';

    public $timestamps = false;

    protected $fillable = [
        'tag_type',
        'tag_name',
        'color',
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'tag_type' => 'required|string|max:20',
            'tag_name' => 'required|string|max:20|unique:tag,tag_name',
            'color' => 'required|string|size:6',
        ]);

        return $validator;
    }
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_tag', 'tag_id', 'event_id');
    }

    public function scopeType($query, array $genres)
    {
        return $query->whereIn('tag_type', $genres);
    }
    //function to get all of the Mood Tags
    public static function getMoodTags()
    {
        return self::where('tag_type', 'Mood')->get();
    }
    //function to get all of the Settings Tags
    public static function getSettingsTags()
    {
        return self::where('tag_type', 'Settings')->get();
    }
    //function to get all of the Music Tags
    public static function getMusicTags()
    {
        return self::where('tag_type', 'Music')->get();
    }
    //function to get all of the Dance Tags
    public static function getDanceTags()
    {
        return self::where('tag_type', 'Dance')->get();
    }
    public static function getTagByTypeAndName($tagType, $tagName)
    {
        $tag = self::where('tag_type', $tagType)
            ->where('tag_name', $tagName)
            ->first();
        return $tag->tag_id;

    }
    public static function getTagNameById($tagId)
    {
        // Retrieve the tag based on the provided tag_id
        $tag = self::find($tagId);
        return $tag->tag_name;
    }

    public static function getTagColorById($tagId)
    {
        // Retrieve the tag based on the provided tag_id
        $tag = self::find($tagId);
        return $tag->color;

    }
    public static function getTag($tagId)
    {
        $tag = self::find($tagId);
        return $tag;
    }
    public static function getTagsByEventId($eventId)
    {
        $tagIds = EventTag::getTagsByEventId($eventId);

        $tags = self::whereIn('tag_id', $tagIds)->get();

        return $tags; 
    }

}
