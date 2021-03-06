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
Auth::routes();

Route::get('/', function () {
    return view('welcome');
});

Route::get('/logout', [App\Http\Controllers\TestCaseController::class, 'logout'])->name('manual-logout');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/fetch-tests', [App\Http\Controllers\TestCaseController::class, 'fetchTests'])->name('fetch-tests');
Route::get('/fetch-single-test/{group}/{environment}/{service}', [App\Http\Controllers\TestCaseController::class, 'fetchSingleTest'])->name('fetch-single-test');

Route::middleware(['auth'])->group(function () {

    Route::group(['middleware' => ['admin']], function () {

        Route::get('/services', function () {
            return view('services');
        });
        Route::get('/shots', function () {
            return view('shots');
        });
        Route::get('/service-contacts', function () {
            return view('service-contacts');
        });
        Route::get('/accounts', function () {
            return view('accounts');
        });
        Route::get('/logs', function () {
            return view('logs');
        });


        Route::get('/fetch-logs', [App\Http\Controllers\HomeController::class, 'fetchLogs'])->name('fetch-logs');
        Route::get('/rerun-single-test/{group}/{environment}/{service}', [App\Http\Controllers\TestCaseController::class, 'rerunSingleTest'])->name('rerun-single-test');

        Route::get('/unpause-test/{test}', [App\Http\Controllers\TestCaseController::class, 'unpauseTest'])->name('unpause-single-test');
        Route::get('/pause-test/{test}', [App\Http\Controllers\TestCaseController::class, 'pauseTest'])->name('pause-single-test');

        Route::get('/unmute-test/{test}', [App\Http\Controllers\TestCaseController::class, 'unmuteTest'])->name('unmute-single-test');
        Route::get('/mute-test/{test}', [App\Http\Controllers\TestCaseController::class, 'muteTest'])->name('mute-single-test');

        Route::get('/fetch-service-contacts/{service?}', [App\Http\Controllers\TestCaseController::class, 'fetchServiceContacts'])->name('fetch-service-contacts');
        Route::get('/remove-service-contacts/{contact}/{service}', [App\Http\Controllers\TestCaseController::class, 'removeServiceContacts'])->name('remove-service-contacts');
        Route::post('/add-service-contacts', [App\Http\Controllers\TestCaseController::class, 'addServiceContacts'])->name('add-service-contacts');

        Route::get('/fetch-accounts/{account?}', [App\Http\Controllers\TestCaseController::class, 'fetchAccounts'])->name('fetch-accounts');
        Route::post('/update-contacts/{contact}', [App\Http\Controllers\TestCaseController::class, 'updateContacts'])->name('update-contacts');
        Route::post('/add-contacts', [App\Http\Controllers\TestCaseController::class, 'addContacts'])->name('add-contacts');

        Route::get('/delete-services/{serv}', [App\Http\Controllers\TestCaseController::class, 'deleteServices'])->name('delete-services');
        Route::post('/update-services/{serv}', [App\Http\Controllers\TestCaseController::class, 'updateServices'])->name('update-services');
        Route::post('/create-services', [App\Http\Controllers\TestCaseController::class, 'createServices'])->name('create-services');

        Route::post('/send-message', [App\Http\Controllers\TestCaseController::class, 'messageContact'])->name('message-contact');
    });
});
