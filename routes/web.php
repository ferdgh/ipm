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

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\IPAMController;


Route::group(['middleware' => 'web'], function () {

	Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
	Route::post('login', [AuthController::class, 'login']);
	Route::get('logout', [AuthController::class, 'logout']);

});

Route::get('/home', function () {
    if(isset(Auth::user()->id))
    {
    	return redirect('/ipam');
    }
    return redirect('login');
});

Route::get('/', function () {
    if(isset(Auth::user()->id))
    {
    	return redirect('ipam');
    }
    return redirect('login');
});


Route::group(['middleware' => ['web', 'auth']], function () {

	Route::get('/ipam', function () {
	    return view('ipam');
	});

	Route::post('add-ip', [IPAMController::class, 'add_ip']);

	Route::any('ipam-serverside', [IPAMController::class, 'ipam_serverside']);
	Route::post('update-desc', [IPAMController::class, 'update_desc']);

	Route::get('desc-logs/{ip_id}', [IPAMController::class, 'desc_logs']);
	Route::any('desc-logs-serverside/{ip_id}', [IPAMController::class, 'desc_logs_serverside']);

	Route::get('login-logs/{ip_id}', [IPAMController::class, 'login_logs']);
	Route::any('login-logs-serverside/{ip_id}', [IPAMController::class, 'login_logs_serverside']);

});
