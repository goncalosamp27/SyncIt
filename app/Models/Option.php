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
    //Relationships
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }

    
    // Many Members can vote for an Option (via the Voting table).
    public function members()
    {
        return $this->belongsToMany(Member::class, 'voting', 'option_id', 'member_id')
                    ->withPivot('poll_id')  
                    ->withTimestamps();
    }

    /**
     * Get the number of votes for this option.
     */
    public function getVoteCount()
    {
        return $this->members()->count();
    }

    /**
     * Check if a member has voted for this option.
     */
    public function hasVoted(Member $member)
    {
        return $this->members()->where('member_id', $member->member_id)->exists();
    }

}
