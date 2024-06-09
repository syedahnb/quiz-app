<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/quiz', [QuizController::class, 'show'])->name('quiz.show');
Route::post('/quiz/store-name', [QuizController::class, 'storeName'])->name('quiz.storeName');
Route::post('/quiz/submit', [QuizController::class, 'submit'])->name('quiz.submit');
Route::get('/quiz/result', [QuizController::class, 'result'])->name('quiz.result');
Route::get('/refresh-csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
})->name('refreshCsrfToken');

Route::get('/check-session', [QuizController::class, 'checkSession'])->name('checkSession');

Route::get('/getUserName', [QuizController::class, 'getUserName'])->name('getUserName');
