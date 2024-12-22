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

    public static function validate($data)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:100',
            'poll_id' => 'required|exists:poll,poll_id',
        ]);

        return $validator;
    }

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id', 'poll_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'voting', 'option_id', 'member_id')
            ->withPivot('poll_id')
            ->withTimestamps();
    }

    public function votes()
    {
        return $this->votes; // Return the 'votes' attribute for this option
    }

    public function countVotes()
    {
        // Count the number of votes (entries in the 'voting' table) for this option
        return \DB::table('voting')->where('option_id', $this->option_id)->count();
    }
    public static function getOptionVoteCountsByPoll($pollId)
    {
        return self::where('option.poll_id', $pollId) // Explicitly specify the table for poll_id
            ->leftJoin('voting', 'option.option_id', '=', 'voting.option_id')
            ->select('option.option_id', \DB::raw('COUNT(voting.voting_id) as vote_count'))
            ->groupBy('option.option_id')
            ->pluck('vote_count', 'option.option_id')
            ->toArray();
    }
    public function calculatePercentage()
    {
        $poll_id = $this->poll->poll_id;

        $totalVotes = $this->poll->calculateTotalVotes($poll_id);

        $optionVotes = $this->countVotes();

        return $totalVotes > 0 ? ($optionVotes / $totalVotes) * 100 : 0;
    }

}
