<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Artist extends Model
{
    use HasFactory;

    protected $table = 'artist';

    public $timestamps = false;

    protected $primaryKey = 'artist_id';

    protected $fillable = [
        'rating',
        'member_id'
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'rating' => 'required|numeric|between:0,5',
        ]);

        return $validator;
    }

    public static function createArtist($data)
{
    // Validate the input data
    $validator = self::validate($data);

    // If validation fails, return the error messages
    if ($validator->fails()) {
        return $validator->errors();
    }

    $artist = new self();

    $artist->rating = $data['rating'];

    $artist->artist_id = $data['member_id']; 

    if ($artist->save()) {
        return $artist;  
    }

}
    // Relationships
    // Artist is a Member 
    public function member()
    {
        return $this->belongsTo(Member::class, 'artist_id', 'member_id');
    }

    // 1 Artist has many Events
    public function events()
    {
        return $this->hasMany(Event::class, 'artist_id', 'artist_id');
    }

    // Many Artists to Many Tags
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'artist_tag', 'artist_id', 'tag_id')
            ->withTimestamps();
    }

    // Artist has followers number associated
    public function getFollowersCount()
    {
        return $this->hasMany(Following::class, 'artist_id', 'artist_id')->count();
    }

    public static function addMemberAsArtist($memberId, $rating = 0)
    {
        // Check if the member is already an artist
        if (self::where('artist_id', $memberId)->exists()) {
            return [
                'success' => false,
                'message' => 'This member is already registered as an artist.',
            ];
        }

        try {
            // Create the new artist entry
            $artist = self::create([
                'artist_id' => $memberId, // Associate with member_id
                'rating' => $rating, // Default or passed rating
            ]);

            // Return the artist_id after successful creation
            return [
                'success' => true,
                'message' => 'Member successfully added as an artist.',
                'artist_id' => $artist->artist_id, // Return the created artist_id
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Failed to add member as an artist, with member_id {$memberId}",
                'error' => $e->getMessage(),
            ];
        }
    }


    public static function getArtistIdByMemberId($memberId)
    {
        // Attempt to find the artist entry with the given member_id
        $artist = self::where('artist_id', $memberId)->first();

        // Return the artist_id if found, otherwise null
        return $artist ? $artist->artist_id : null;
    }

    public function getAverageRatingAttribute()
    {
        return $this->events->avg(function ($event) {
            return $event->ratings()->avg('rating');
        });
    }

}
