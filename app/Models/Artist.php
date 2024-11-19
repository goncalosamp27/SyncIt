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
            'rating' => 'required|numeric|between:0,5',  // rating_domain: DECIMAL(2,1) between 0.0 and 5.0
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
    //association 

    public function member()
    {
        return $this->belongsTo(Member::class, 'artist_id', 'member_id');
    }
}
