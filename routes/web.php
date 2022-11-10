<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleMapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/map', [GoogleMapController::class, 'map'])->name('map');
Route::get('/map/{name}', [GoogleMapController::class, 'getLocationFromText'])->name('map.location');
Route::get('/mapnearby', [GoogleMapController::class, 'getNearbyPlaces'])->name('map.nearby');
