<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Notifications\CommentNotificationController;
use App\Http\Controllers\Notifications\FollowNotificationController;
use App\Http\Controllers\Notifications\InvitationNotificationController;
use App\Http\Controllers\Notifications\PollNotificationController;
use App\Http\Controllers\Notifications\RestrictionNotificationController;

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

Route::get('/create', function () {
    return view('pages.create');
});

Route::get('/admin', function () {
    return view('pages.admin');
});

Route::get('/admin', [AdminController::class, 'display_members']);

Route::get('/admin/edit/member/{id}', [AdminController::class, 'getMember'])->name('admin.edit.member');
//Route::put('/admin/edit/member/{id}', [AdminController::class, 'updateMember']);


Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');

Route::get('/event/{event_id}', [EventController::class, 'show'])->name('event');

Route::get('/events', [TagController::class, 'showTagsPerType'])->name('events');

// Authentication
Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::post('/login', 'authenticate');
    Route::get('/logout', 'logout')->name('logout');
});

Route::controller(RegisterController::class)->group(function () {
    Route::get('/register', 'showRegistrationForm')->name('register');
    Route::post('/register', 'register');
});

