<?php
use Illuminate\Support\Facades\Route;

/* REMOVE LATER */
use App\Http\Controllers\CardController;
use App\Http\Controllers\ItemController;
/* REMOVE LATER */

use App\Http\Controllers\ArtistController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventTagController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\HomeController;

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

Route::get('/artist/{artistId}', function ($artistId) {
    // Fetch the artist and its related events
    $artist = Artist::with('events')->find($artistId);
    
    // Handle if the artist is not found
    if (!$artist) {
        abort(404, 'Artist not found');
    }

    $followersCount = $artist->getFollowersCount();
    return view('pages.artist', ['artist' => $artist, 'followersCount' => $followersCount]);
});

Route::get('/create', function () {
    return view('pages.create');
});

Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');

Route::get('/event',function() {
    return view('pages.event');
});

Route::get('/events', [EventController::class, 'display_events']);

Route::get('/login',function() {
    return view('pages.login');
});

// Cards
Route::controller(CardController::class)->group(function () {
    Route::get('/cards', 'list')->name('cards');
    Route::get('/cards/{id}', 'show');
});


// API
Route::controller(CardController::class)->group(function () {
    Route::put('/api/cards', 'create');
    Route::delete('/api/cards/{card_id}', 'delete');
});

Route::controller(ItemController::class)->group(function () {
    Route::put('/api/cards/{card_id}', 'create');
    Route::post('/api/item/{id}', 'update');
    Route::delete('/api/item/{id}', 'delete');
});


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

