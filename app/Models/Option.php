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

    protected $fillable = [
        'poll_id',
        'name',
        'votes',
    ];

    /**
     * Validate the data for creating or updating an Option.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',  // Changed from 'option_name' to 'name' to match the column name
            'poll_id' => 'required|exists:poll,poll_id',  
        ]);

        return $validator;
    }

    // Relationships

    /**
     * The poll that the option belongs to.
     */
    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }

    /**
     * Many Members can vote for an Option via the Voting table.
     */
    public function members()
    {
        return $this->belongsToMany(Member::class, 'voting', 'option_id', 'member_id')
                    ->withPivot('poll_id')  // This allows you to access 'poll_id' in the pivot table
                    ->withTimestamps();
    }

    /**
     * Get the number of votes for this option.
     */
    public function getVoteCount()
    {
        return $this->members()->count();  // Returns the number of members who voted for this option
    }

    /**
     * Check if a member has voted for this option.
     */
    public function hasVoted(Member $member)
    {
        return $this->members()->where('member_id', $member->member_id)->exists();
    }
}
