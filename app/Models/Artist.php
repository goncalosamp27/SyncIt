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
        $validator = self::validate($data);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return self::create($data);
    }
    //Relationships
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
    

    public function getFollowersCount()
    {
        return $this->hasMany(Following::class, 'artist_id', 'artist_id')->count();
    }


}
