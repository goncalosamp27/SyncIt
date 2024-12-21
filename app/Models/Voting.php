<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Voting extends Model
{
    use HasFactory;

    protected $table = 'voting';

    protected $primaryKey = 'voting_id';

    public $timestamps = false;

    protected $fillable = [
        'poll_id',
        'option_id',
        'member_id',
    ];

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'poll_id' => 'required|exists:poll,poll_id',       
            'option_id' => 'required|exists:poll_option,id', 
            'member_id' => 'required|exists:member,member_id', 
            'unique' => 'unique:voting,poll_id,NULL,NULL,member_id,' . $data['member_id'],
        ]);

        return $validator;
    }

    // Define the relationship with Poll model
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    // Define the relationship with Option model
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    // Define the relationship with Member model
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
}
