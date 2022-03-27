<?php

use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

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


Route::controller(EmployeeController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/loading', 'loading')->name('loading');
    Route::post('/save', 'save')->name('save');
    Route::post('/get-details', 'getDetails')->name('get-details');
    Route::post('/update', 'update')->name('update');
    Route::post('/delete', 'delete')->name('delete');
});