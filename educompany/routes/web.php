<?php

use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AuthController;
use \App\Http\Controllers\frontend\CommonController;
use App\Http\Controllers\frontend\RoutesController;
use App\Http\Controllers\FunctionsController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']], function () {

    Route::group([
        'namespace' => 'App\\Http\\Controllers\\frontend'
    ], function () {
        Route::get('pages/{slug}', [RoutesController::class, 'standartpage'])->name('pages.show');
        Route::get('/', [RoutesController::class, 'welcome'])->name('page.welcome');
        Route::get('search', [RoutesController::class, 'search'])->name('action.search');
        Route::get("examinations", [RoutesController::class, 'exams'])->name("front.exams.index");
        Route::get("examinations/{slug}", [RoutesController::class, 'examinfo'])->name("front.exams.show");
        Route::get("blogs_front/{slugs}", [RoutesController::class, 'blogs_front'])->name("blogs_front.show");
        Route::get("teams_front/{slugs}", [RoutesController::class, 'teams_front'])->name("teams_front.show");
        Route::get('/exams', [HomeController::class, 'exams'])->name('exams_front.index');
        Route::get('/exams/{slug}', [HomeController::class, 'showexam'])->name('exams.show');
        Route::get('/exams/{category_id?}', [HomeController::class, 'exams'])->name('exams');
        Route::get('/category_exam/{category?}', [HomeController::class, 'exams'])->name('category_exam');

        Route::middleware(['user.guest'])->group(function () {
            Route::get('/login', [AuthController::class, 'login'])->name('login');
            Route::get('/register', [AuthController::class, 'register'])->name('register');
            Route::post('/authenticate', [AuthController::class, 'authenticate'])->name('user.authenticate');
            Route::post('/register-save', [AuthController::class, 'registerSave'])->name('user.register');
            Route::get('/email', [AuthController::class, 'email'])->name('email');
            Route::post('/send-token', [AuthController::class, 'sendToken'])->name('send.token');
            Route::get('/reset/{token}', [AuthController::class, 'reset'])->name('reset');
            Route::post('/change-password', [AuthController::class, 'changePassword'])->name('change.password');
        });

        Route::group(['middleware' => 'users', 'as' => 'user.'], function () {
            Route::get('profile', [AuthController::class, 'profile'])->name('profile');
            Route::any('/logout', [AuthController::class, 'logout'])->name('logout');

            Route::group(['prefix' => 'exam'], function () {
                Route::get('results', [CommonController::class, 'examResults'])->name('exam.results');
                Route::get('resultpage/{result_id}', [CommonController::class, 'examResultPage'])->name('exam.resultpage');
                Route::get('results/{result_id}', [CommonController::class, 'examResult'])->name('exam.result');
                Route::get("exams/redirect_exam",[CommonController::class,'redirect_exam'])->name("exams.redirect_exam");
                Route::any("exams/set_exam",[CommonController::class,'set_exam'])->name("exams.set_exam");
                // Route::get('/{exam_id}', [CommonController::class, 'exam'])->name('exam');
                Route::any('/finish', [CommonController::class, 'examFinish'])->name('exam.finish')->middleware('remove.null_value');
            });
        });

        Route::fallback([CommonController::class, 'notfound'])->name('notfound');
        // Route::post("sendmessage", [FunctionsController::class, 'sendmessage'])->name("contactus.sendmessage");

    });
});

