<?php

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

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Route::domain('ad.adsso.test')->group(function () {
//     Route::get('/aad/teste', function (){
//         return view('homePage');
//     });

//     Route::get('/aad/dashboard', function (){
//         return view('dashboard');
//     });
// });

Route::middleware(['saml', 'auth'])->group(function(){
    Route::get('saml2/aad/teste', function (){
        return view('homePage');
    });

    Route::get('saml2/aad/dashboard', function (){
        return view('dashboard');
    });
});

// Route::group([ 'prefix' => '/saml2' ], function () {
// 	Route::get('/aad/teste', function (){
//         return view('homePage');
//     });

//     Route::get('/aad/dashboard', function (){
//         return view('dashboard');
//     });
// });