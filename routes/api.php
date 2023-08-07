<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConcertController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/concerts', [ConcertController::class, 'getConcerts'])->name('getConcerts');
Route::get('/concerts/{concert}', [ConcertController::class, 'showConcert'])->name('showConcert');
Route::get('/concerts/{concert_id}/shows/{show_id}/seating', [ConcertController::class, 'seating'])->name('getSeating');
Route::post('/concerts/{concert_id}/shows/{show_id}/reservation', [ConcertController::class, 'reservation'])->name('setReservation');
Route::post('/concerts/{concert_id}/shows/{show_id}/reservation', [ConcertController::class, 'reservation'])->name('setReservation');
Route::post('/concerts/{concert_id}/shows/{show_id}/booking', [ConcertController::class, 'booking'])->name('setBooking');
