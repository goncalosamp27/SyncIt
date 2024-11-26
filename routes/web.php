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
use App\Http\Controllers\EditEventController;
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

Route::redirect('/', '/home');
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/artist/{artist_id}', [ArtistController::class, 'show'])->name('artist');

Route::controller(AdminController::class)->middleware('admin')->group(function () {
    Route::get('admin', 'display_members')->name('admin');
    Route::get('admin/search', 'search')->name('members.search');
    Route::get('admin/edit/member/{id}', 'getMember')->name('admin.edit.member');
    Route::put('admin/edit/member/{id}', 'updateMemberAdmin')->name('member.updates');
    Route::get('admin/register', 'createMember')->name('create.member');
});

Route::controller(EventController::class)->group(function () {
    Route::get('/event/{event_id}', 'show')->name('event');
    Route::get('/events', 'showTagsPerType')->name('events');
    Route::get('/past-events', 'showTagsPerTypePast')->name('past-events');
    Route::get('/future-events', 'showTagsPerTypeFuture')->name('future-events');
    Route::get('/events/search', 'search')->name('events.search');
    Route::get('/event/{event_id}/participants', 'tickets')->name('participants');
    Route::middleware(['notAdmin', 'auth'])->group(function () {
        Route::get('/events/create', 'create')->name('events.create');
        Route::post('/events/store', 'store')->name('events.store');
        Route::get('/your-events', 'member_events')->name('your-events');
        Route::post('/your-events/{event_id}', 'deleteEvent')->name('delete-event');
    });
});
Route::get('/event/{event_id}', [EventController::class, 'show'])->name('event');
Route::get('/events/create', [EventController::class, 'create'])->middleware('auth')->name('events.create');
Route::post('/events/store', [EventController::class, 'store'])->name('events.store');
Route::get('/events', [EventController::class, 'showTagsPerType'])->name('events');
Route::get('/past-events', [EventController::class, 'showTagsPerTypePast'])->name('past-events');
Route::get('/future-events', [EventController::class, 'showTagsPerTypeFuture'])->name('future-events');
Route::post('/future-events/filter', [EventController::class, 'filterEvents'])->name('events.filter');
Route::get('/events/search', [EventController::class, 'search'])->name('events.search');

Route::post('/event/buy-ticket', [TicketController::class, 'buyTicket'])
    ->name('buy-ticket')
    ->middleware('auth');
Route::post('/events/getTags', [EventController::class, 'getTags'])->name('events.filters.tags');

//AJAX
Route::post('/future-events/updateFutureEventsPage', [EventController::class, 'updateFutureEventsPage'])->name('future-events-update');
Route::post('/future-events/getEventCards', [EventController::class, 'getEventCards'])->name('get-cards');

Route::post('/tickets/{ticket_id}', [TicketController::class, 'refundTicket'])->name('refund-ticket');
Route::post('/your-events/{event_id}', [EventController::class, 'deleteEvent'])->name('delete-event');

Route::controller(TicketController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::post('/event/buy-ticket', 'buyTicket')->name('buy-ticket');
    Route::post('/tickets/{ticket_id}', 'refundTicket')->name('refund-ticket');
    Route::get('/tickets', 'ticketAndEventData')->name('tickets');
});

Route::post('/create-invitation', [InvitationController::class, 'create'])->middleware(['notAdmin', 'auth'])->name('create-invitation');

Route::controller(NotificationController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/notifications', 'getNotifications')->name('notifications');
    Route::post('/notifications/{notification_id}', 'deleteNotification')->name('delete-notification');
});

Route::controller(MemberController::class)->middleware(['notAdmin', 'auth'])->group(function () {
    Route::get('/edit_profile', 'edit')->name('profile.edit');
    Route::put('/edit_profile', 'updateMember')->name('member.profile.edit');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->middleware(['visitor'])->name('login');
    Route::post('/login', 'authenticate')->middleware(['visitor']);
    Route::get('/logout', 'logout')->middleware(['userAdmin'])->name('logout');
});

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
    Route::post('/event/edit/{event_id}/{ticket_id}', 'deleteParticipant')->name('delete-participant');
});



