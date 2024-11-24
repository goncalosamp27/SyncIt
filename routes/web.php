<?php
use App\Http\Controllers\CreateEventController;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Models\Artist;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root URL to home
Route::redirect('/', '/home');

// Add this to render the home view
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/tickets', function () {
    return view('pages.tickets');
});
Route::get('/notifications', [NotificationController::class, 'getNotifications']);

Route::get('/artist/{artist_id}', [ArtistController::class, 'show'])->name('artist');

/*
Route::get('/artist/{artistId}', function ($artistId) {
    // Fetch the artist and its related events
    $artist = Artist::with('events')->find($artistId);
    
    // Handle if the artist is not found
    if (!$artist) {
        abort(404, 'Artist not found');
    }

    $followersCount = $artist->getFollowersCount();
    return view('pages.artist', ['artist' => $artist, 'followersCount' => $followersCount]);
});*/

/*
Route::get('/create', function () {
    return view('pages.create');
});
*/

Route::get('/admin', function () {
    return view('pages.admin');
});

Route::get('/admin', [AdminController::class, 'display_members'])->name('admin');

Route::get('/admin/edit/member/{id}', [AdminController::class, 'getMember'])->name('admin.edit.member');
//Route::put('/admin/edit/member/{id}', [AdminController::class, 'updateMember']);

Route::get('/event/{event_id}', [EventController::class, 'show'])->name('event');
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth')->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
Route::get('/events', [EventController::class, 'showTagsPerType'])->name('events');
Route::get('/past-events', [EventController::class, 'showTagsPerTypePast'])->name('past-events');
Route::get('/future-events', [EventController::class, 'showTagsPerTypeFuture'])->name('future-events');

Route::post('/event/buy-ticket', [TicketController::class, 'buyTicket'])
    ->name('buy-ticket')
    ->middleware('auth');

Route::post('/tickets/{ticket_id}', [TicketController::class, 'refundTicket'])->name('refund-ticket');
Route::post('/your-events/{event_id}', [EventController::class, 'deleteEvent'])->name('delete-event');

Route::middleware('auth')->group(function () {
    Route::get('/your-events', [EventController::class, 'member_events'])->name('your-events');
});
Route::middleware('auth')->group(function () {
    Route::get('/tickets', [TicketController::class, 'ticketAndEventData'])->name('tickets');
});

Route::get('/event/{event_id}', [EventController::class, 'show'])->name('event');
Route::get('/event/{event_id}/edit', [EventController::class, 'editEvent'])->name('edit.event');
Route::get('/event/{event_id}/participants', [EventController::class, 'participants'])->name('participants');

Route::get('/edit_profile', [MemberController::class, 'edit'])->name('profile.edit');

Route::get('/events', [EventController::class, 'showTagsPerType'])->name('events');
Route::post('/create-invitation', [InvitationController::class, 'create'])->name('create-invitation');

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

Route::get('/create', [CreateEventController::class, 'show'])->name('create.show');
Route::post('/create', [CreateEventController::class, 'store'])
    ->middleware('auth')
    ->name('create.store');

