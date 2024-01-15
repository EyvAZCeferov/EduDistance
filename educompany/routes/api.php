<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\backend\ExamController;
use App\Http\Controllers\frontend\ApisController;
use App\Http\Controllers\frontend\CommonController;

Route::get("flush", function () {
    Cache::flush();
    return "Cache OK";
});
Route::post('searchinfilled', [ApisController::class, 'searchinfilled'])->name("api.searchinfilled");
Route::post('filterelements', [ApisController::class, 'filterelements'])->name("api.filterelements");
Route::post("check_coupon_code", [ApisController::class, 'check_coupon_code'])->name("api.check_coupon_code");
Route::post('mark_unmark_question', [ExamController::class, 'mark_unmark_question'])->name("api.mark_unmark_question");
Route::any('finish_exam', [CommonController::class, 'examFinish'])->name('finish_exam')->middleware('remove.null_value');
Route::post('getsectiondata',[ApisController::class,'getsectiondata'])->name("api.getsectiondata");
Route::post('getsectioninformation',[ApisController::class,'getsectioninformation'])->name("api.getsectioninformation");
Route::post('setsectiondata',[ApisController::class,'setsectiondata'])->name("api.setsectiondata");
Route::post('getexamsections',[ApisController::class,'getexamsections'])->name("api.getexamsections");
Route::post('get_markedquestions_users',[ApisController::class,'get_markedquestions_users'])->name("api.get_markedquestions_users");
