<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\ExamController;
use App\Http\Controllers\frontend\ApisController;
use App\Http\Controllers\frontend\CommonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get("flush", function () {
    Cache::flush();
    return "Cache OK";
});
Route::post('searchinfilled', [ApisController::class, 'searchinfilled'])->name("api.searchinfilled");
Route::post('filterelements', [ApisController::class, 'filterelements'])->name("api.filterelements");
Route::post("check_coupon_code", [ApisController::class, 'check_coupon_code'])->name("api.check_coupon_code");
Route::post('mark_unmark_question', [ExamController::class, 'mark_unmark_question'])->name("api.mark_unmark_question");
Route::any('finish_exam', [CommonController::class, 'examFinish'])->name('finish_exam')->middleware('remove.null_value');
