<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Option extends Model
{
    use HasFactory;

    protected $table = 'option';

    protected $primaryKey = 'option_id';

    public $timestamps = false;

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'option_name' => 'required|string|max:100',  
            'poll_id' => 'required|exists:poll,poll_id',  
        ]);

        return $validator;
    }
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }

}
