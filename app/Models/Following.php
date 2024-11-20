<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Following extends Model
{
    use HasFactory;

    protected $table = 'following';

    public $timestamps = false;

    //association
    public function artist()
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'artist_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }
    
}
