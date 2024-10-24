<?php

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ShowController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
    Route::post('/hall', [App\Http\Controllers\HallController::class, 'store'])->name('hall.store');
    Route::get('/halls/{id}', [App\Http\Controllers\HallController::class, 'show'])->name('hall.show');
    Route::delete('/halls/{id}', [App\Http\Controllers\HallController::class, 'destroy'])->name('hall.destroy');
    Route::post('/halls/{id}/dimentions', [App\Http\Controllers\HallController::class, 'updateDimentions'])->name('hall.updateDimentions');
    Route::get('/halls/{id}/price', [App\Http\Controllers\HallController::class, 'getPrice'])->name('hall.getPrice');
    Route::post('/halls/{id}/price', [App\Http\Controllers\HallController::class, 'updatePrice'])->name('hall.updatePrice');
    Route::get('/check-seats-and-shows', [App\Http\Controllers\HallController::class, 'checkSeatsAndShows'])->name('hall.checkSeatsAndShows');
    Route::post('/halls/{hallId}/activateHall', [App\Http\Controllers\HallController::class, 'activateHall'])->name('hall.activateHall');
    Route::post('/seats/{hallId}', [App\Http\Controllers\SeatController::class, 'store'])->name('seat.store');
    Route::get('/seats/{hallId}', [App\Http\Controllers\SeatController::class, 'show'])->name('seat.show');
    Route::post('/movies', [App\Http\Controllers\MovieController::class, 'store'])->name('movie.store');
    Route::delete('/movies/{id}', [App\Http\Controllers\MovieController::class, 'destroy'])->name('movie.destroy');
    Route::post('/shows', [App\Http\Controllers\ShowController::class, 'store'])->name('show.store');
    Route::delete('/shows/{id}', [App\Http\Controllers\ShowController::class, 'destroy'])->name('show.destroy');
});

Route::get('/', [App\Http\Controllers\ClientController::class, 'index']);
Route::get('/get-content', [App\Http\Controllers\ClientController::class, 'getContent'])->name('client.getContent');
Route::get('/hall/{show}', [App\Http\Controllers\BookingController::class, 'showHall'])->name('client.hall');
Route::post('/store-seats', [App\Http\Controllers\BookingController::class, 'storeSelectedSeats'])->name('client.store-seats');
Route::get('/payment', [App\Http\Controllers\BookingController::class, 'showPayment'])->name('client.payment');
Route::post('/process-booking', [App\Http\Controllers\BookingController::class, 'process'])->name('bookings.process');
Route::get('/booking/ticket', [App\Http\Controllers\BookingController::class, 'showTicket'])->name('booking.ticket');
Route::get('/api/show-seats/{showId}', [App\Http\Controllers\ShowController::class, 'getShowSeats']);
