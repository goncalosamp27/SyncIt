<?php
use App\Http\Controllers\CreateEventController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Models\Tag;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\EditEventController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CommentVoteController;
use App\Http\Controllers\JoinRequestController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\PollDataController;


use App\Models\Artist;

Route::redirect('/', '/home');
Route::redirect('/admin', '/admin/members/active');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/artist/{artist_id}', [ArtistController::class, 'show'])->name('artist');
Route::get('/artists', [ArtistController::class, 'display_artists'])->name('artists');
Route::get('/artists/search', [ArtistController::class, 'search'])->name('artists.search');

Route::controller(AdminController::class)->middleware('admin')->group(function () {
    Route::get('admin/members/{status}', 'getMembersByStatus')->name('admin');
    Route::get('admin/members/active/search', 'search')->name('members.search');
    Route::get('admin/edit/member/{id}', 'getMember')->name('admin.edit.member');
    Route::put('admin/edit/member/{id}', 'updateMemberAdmin')->name('member.updates');
    Route::get('admin/register', 'createMember')->name('create.member');
    Route::put('admin/members/', 'applyRestriction')->name('admin.restrict.member');
    Route::put('admin/{id}', 'removeRestriction')->name('admin.remove.restriction');
});

Route::controller(EventController::class)->group(function () {
    Route::get('/event/{event_id}', 'show')->name('event');
    Route::get('/events', 'showTagsPerType')->name('events');
    Route::get('/past-events', 'showTagsPerTypePast')->name('past-events');
    Route::get('/future-events', 'showTagsPerTypeFuture')->name('future-events');
    Route::get('/events/search', 'search')->name('events.search');
    Route::get('/event/{event_id}/participants', [EditEventController::class, 'tickets'])->name('participants');
    Route::middleware(['notAdmin', 'auth'])->group(function () {
        Route::get('/events/create', 'create')->name('events.create');
        Route::post('/events/store', 'store')->name('events.store');
        Route::get('/your-events', 'member_events')->name('your-events');
        Route::post('/your-events/{event_id}', 'deleteEvent')->name('delete-event');
    });
});

Route::post('/event/{event_id}/report', [ReportController::class, 'createReport'])->name('create.report');
Route::get('/admin/reports/{status}', [ReportController::class, 'showReports'])->middleware('admin')->name('admin.reports');
Route::put('/admin/reports/{report}/mark-solved', [ReportController::class, 'markAsSolved'])->middleware('admin')->name('reports.markSolved');

Route::get('/event/{event_id}', [EventController::class, 'show'])->name('event');
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth')->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
Route::get('/events', [EventController::class, 'showTagsPerType'])->name('events');
Route::get('/past-events', [EventController::class, 'showTagsPerTypePast'])->name('past-events');
Route::get('/future-events', [EventController::class, 'showTagsPerTypeFuture'])->name('future-events');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');
Route::post('/event/{event_id}/cancel', [EventController::class, 'cancelEvent'])->name('event.cancel');
Route::post('/events/getTags', [EventController::class, 'getTags'])->name('events.filters.tags');

Route::controller(RatingController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::post('/rate-event/{ticket_id}', 'rateEvent')->name('rate-event');
});

Route::post('/event/buy-ticket', [TicketController::class, 'buyTicket'])->name('buy-ticket')->middleware('auth');

Route::post('/event/request-access', [JoinRequestController::class, 'requestAccess'])->name('request-access')
    ->middleware(['auth', 'notAdmin']);

Route::controller(CommentController::class)->middleware(['auth'])->group(function () {
    Route::post('/event/{event_id}/comments', 'store')->name('comments.store');
    Route::delete('/comments/{comment_id}', 'destroy')->name('comments.destroy');
    Route::put('/update-comment/{comment_id}', 'update')->name('comments.update');
});

Route::get('/load-more-events', [EventController::class, 'loadMoreEvents']);
Route::get('/load-more-artists', [ArtistController::class, 'loadMoreArtists']);

Route::get('/event/{event_id}/comments', [CommentController::class, 'index'])->name('comments.index');

Route::post('/comments/{comment_id}/vote', [CommentVoteController::class, 'voteComment'])->middleware('auth');

//AJAX
Route::post('/future-events/filter', [EventController::class, 'filterEvents'])->name('events.filter');
Route::post('/future-events', function (Request $request) {
    Log::info($request->input('tagsMusic'));
    $events = $request->input('events');
    $tagsMusic = $request->input('tagsMusic');
    $tagsDance = $request->input('tagsDance');
    $tagsMood = $request->input('tagsMood');
    $tagsSettings = $request->input('tagsSettings');

    foreach ($events as &$event) {
        $eventTags = Tag::getTagsByEventId($event['event_id'])->take(3);
    
        $event['tags'] = $eventTags;
    }
    return view('pages.events', [
        'events' => $events ,
        'tagsMusic' => $tagsMusic ,
        'tagsDance' => $tagsDance ,
        'tagsMood' => $tagsMood ,
        'tagsSettings' => $tagsSettings ,
    ]);
    
});

Route::post('/tickets/{ticket_id}', [TicketController::class, 'refundTicket'])->name('refund-ticket');
Route::post('/your-events/{event_id}', [EventController::class, 'deleteEvent'])->name('delete-event');

Route::controller(TicketController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::post('/event/buy-ticket', 'buyTicket')->name('buy-ticket');
    Route::post('/tickets/{ticket_id}', 'refundTicket')->name('refund-ticket');
    Route::get('/tickets', 'ticketAndEventData')->name('tickets');
    Route::get('/attended', 'ticketAndEventData2')->name('attended-events');
});

Route::post('/create-invitation', [InvitationController::class, 'create'])->middleware(['notAdmin', 'auth'])->name('create-invitation');
Route::post('/create-invitation2', [InvitationController::class, 'create2'])->middleware(['notAdmin', 'auth'])->name('create-invitation2');
Route::get('/invitations', [InvitationController::class, 'memberinvitations'])->middleware(['notAdmin','auth'])->name('invitations');
Route::post('/invitations/{invitation_id}', [InvitationController::class, 'deleteInvitation'])->middleware(['notAdmin','auth'])->name('delete-invitation');

Route::controller(NotificationController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/notifications', 'getNotifications')->name('notifications');
    Route::post('/notifications/{notification_id}', 'deleteNotification')->name('delete-notification');
});

Route::controller(MemberController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/edit_profile', 'edit')->name('profile.edit');
    Route::put('/edit_profile', 'updateMember')->name('member.profile.edit');
    Route::post('/account/delete', [MemberController::class, 'delete'])->name('account.delete');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->middleware(['visitor'])->name('login');
    Route::post('/login', 'authenticate')->middleware(['visitor']);
    Route::get('/logout', 'logout')->middleware(['userAdmin'])->name('logout');
});
//Reset password
//View to forgot-password page
Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->middleware(['visitor'])->name('password.request');
//View to Post the email to the server
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
//View to Reset password, constructed by Laravel's defaults
Route::get('reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset'); 
//update a password in db
Route::post('reset-password', [ForgotPasswordController::class, 'submitPasswordForm'])->name('password.reset.submit'); 


Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->middleware(['visitor'])->name('register');
    Route::post('/register', 'register')->name('post.register');
});

Route::controller(CreateEventController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/create', 'show')->name('create.show');
    Route::post('/create', 'store')->name('create.store');
});

Route::controller(EditEventController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/event/edit/{event_id}', 'show')->name('edit.event.show');
    Route::put('/event/edit/{event_id}', 'editEvent')->name('edit.event');
    Route::post('/event/edit/{event_id}/{member_id}', [EditEventController::class, 'deleteParticipant'])->name('delete-participant');
});

Route::view('/about-us', 'pages/about-us')->name('about-us');
Route::view('/contacts', 'pages/contacts')->name('contacts');
Route::view('/services', 'pages/services')->name('services');
Route::view('/faq', 'pages/faq')->name('faq');

Route::post('/file/upload', [FileController::class, 'upload']);

//polls
Route::get('/create-poll/{event_id}', [PollController::class, 'showCreatePoll'])->name('poll.create');
Route::post('/create-poll/{event_id}', [PollController::class, 'storePoll'])->name('poll.store');
Route::post('/poll-vote', [PollController::class, 'storeVote'])->name('poll.vote');
Route::post('/poll-data/{poll_id}', [PollController::class, 'fetchPollData'])->name('poll.data');
Route::get('/data-poll/{poll_id}', [PollDataController::class, 'showDataPoll'])->name('poll-data.show');
