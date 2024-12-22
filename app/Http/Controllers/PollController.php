<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Event;
use App\Models\Voting;
use App\Models\Option;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
class PollController extends Controller
{
    public function showCreatePoll(string $event_id)
    {
        $event = Event::findOrFail($event_id);
        return view(
            'pages.create-poll',
            ['event' => $event]
        );
    }
    public function storePoll(Request $request, string $event_id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $poll = Poll::create([
            'event_id' => $event_id,
            'title' => $request->title,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        // Create options for the poll
        foreach ($request->options as $option) {
            $poll->options()->create(['name' => $option]);
        }

        $polls = Poll::getPollsByEventId($event_id);

        $event = Event::findOrFail($event_id);
        $comments = $event->comments ?: collect();
        return redirect()->route('event', ['event_id' => $event_id])
            ->with([
                'event' => $event,
                'polls' => $polls,
                'comments' => $comments
            ]);
    }

    public function storeVote(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'poll_id' => 'required|exists:poll,poll_id',
            'option_id' => 'required|exists:option,option_id',
            'member_id' => 'required|exists:member,member_id',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        $validatedData = $request->all();

        Log::info('Vote request received:', $validatedData);

        try {
            $existingVote = Voting::where('poll_id', $validatedData['poll_id'])
                ->where('member_id', $validatedData['member_id'])
                ->first();
                Log::info("Existing Vote" . $existingVote);


            if ($existingVote) {
                Log::info('User has already voted for this poll. Member ID: ' . $validatedData['member_id']);
                $updatedVotes = Voting::where('option_id', $validatedData['option_id'])->count();
                Log::info('Number of votes' . $updatedVotes);
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted for this option.',
                    'votes' => $updatedVotes,
                ]);
            } else {
                Log::info('No existing vote. Storing new vote for Member ID: ' . $validatedData['member_id']);
                $voting = Voting::create([
                    'poll_id' => $validatedData['poll_id'],
                    'option_id' => $validatedData['option_id'],
                    'member_id' => $validatedData['member_id'],
                ]);
                Log::info('New vote stored successfully.');
                $updatedVotes = Voting::where('option_id', $validatedData['option_id'])->count();
            }

            $updatedVotes = Voting::where('option_id', $validatedData['option_id'])->count();

            return response()->json([
                'success' => true,
                'message' => 'Vote recorded successfully.',
                'voting' => $voting,
                'votes' => $updatedVotes,
            ]);

        } catch (\Exception $e) {
            Log::error('Error while processing the vote. Message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the vote.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }



    //function to fetch the poll data 
    public function fetchPollData(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'poll_id' => 'required|integer|exists:poll,poll_id',
            ]);

            $pollId = $request->input('poll_id');

            // Fetch vote counts
            $voteCounts = Option::getOptionVoteCountsByPoll($pollId);

            if (empty($voteCounts)) {
                $voteCounts = [];
            }
            

            // Return success response with vote counts
            return response()->json([
                'success' => true,
                'votes' => $voteCounts,
            ], 200);
        } catch (\Exception $e) {
            // Handle any exception that occurs in the try block
            Log::error('Error fetching poll data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }




}
