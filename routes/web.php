<?php

use App\Http\Controllers\LoginController;
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

use App\Http\Controllers\PageController;
use App\Http\Controllers\PurchaseReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
})->middleware('auth');
Route::get('/auth/{token}', [LoginController::class, 'auth'])->name('auth');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/report-pembelian/{report}/data', [PurchaseReportController::class, 'data'])->whereIn('report', ['rppo', 'rppnb', 'rpfkb', 'rppby']);
    Route::get('/report-pembelian/{report}/export/{format}', [PurchaseReportController::class, 'export'])->whereIn('report', ['rppo', 'rppnb', 'rpfkb', 'rppby'])->whereIn('format', ['excel', 'pdf', 'print']);
    Route::get('/profile', [PageController::class, 'profile'])->name('profile'); // view profile
    Route::post('/profile/update', [PageController::class, 'update'])->name('profile.update'); // update profle
    Route::get('/changepass', [PageController::class, 'changepass'])->name('changepass'); // view change password
    Route::post('/changepass/update', [PageController::class, 'changepass_update'])->name('changepass.update'); // update password
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/{page}', [PageController::class, 'index'])->name(''); // route list
    Route::post('/{page}', [PageController::class, 'index'])->name(''); // route store
    Route::get('/{page}/{action}', [PageController::class, 'index'])->name(''); // route show, add, edit
    Route::put('/{page}/{action}', [PageController::class, 'index'])->name(''); // route update
    Route::delete('/{page}/{action}', [PageController::class, 'index'])->name(''); // route delete(destroy)
    Route::get('/{page}/{action}/{id}', [PageController::class, 'index'])->name(''); // route CRUD
});
