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
}
