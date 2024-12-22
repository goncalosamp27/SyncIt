<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Event;
use App\Models\Voting;
use App\Models\Option;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class PollDataController extends Controller
{
    public function showDataPoll(string $poll_id)
    {
        $poll = Poll::findOrFail($poll_id);
        return view(
            'paartials.poll-data',
            ['poll' => $poll]
        );
    }
   

}
