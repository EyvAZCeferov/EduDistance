<?php

use App\Http\Controllers\frontend\ApisController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\AuthController;
use \App\Http\Controllers\frontend\CommonController;
use App\Http\Controllers\frontend\RoutesController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\SetSubdomain;

Route::group(['prefix' => \Mcamara\LaravelLocalization\Facades\LaravelLocalization::setLocale(), 'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','setsubdomain']], function () {

    Route::group([
        'namespace' => 'App\\Http\\Controllers\\frontend',
    ], function () {
        Route::get('pages/{slug}', [RoutesController::class, 'standartpage'])->name('pages.show');
        Route::get('/', [RoutesController::class, 'welcome'])->name('page.welcome');
        Route::get('search', [RoutesController::class, 'search'])->name('action.search');
        Route::get('/exams', [HomeController::class, 'exams'])->name('exams_front.index');
        Route::get('/createoreditexam', [HomeController::class, 'createoreditexam'])->name('exams_front.createoredit');
        Route::get('/exams/{slug}', [HomeController::class, 'showexam'])->name('exams.show');
        Route::get('/exams/{category_id?}', [HomeController::class, 'exams'])->name('exams');
        Route::get('/category_exam/{category?}', [HomeController::class, 'category_exam'])->name('category_exam');

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
                Route::post('add_edit_exam', [CommonController::class, 'add_edit_exam'])->name("exam.add_edit_exam");
                Route::get('results', [CommonController::class, 'examResults'])->name('exam.results');
                Route::get('resultpage/{result_id}', [CommonController::class, 'examResultPage'])->name('exam.resultpage');
                Route::get('results/{result_id}', [CommonController::class, 'examResult'])->name('exam.result');
                Route::get("exams/redirect_exam", [CommonController::class, 'redirect_exam'])->name("exams.redirect_exam");
                Route::any("exams/set_exam", [CommonController::class, 'set_exam'])->name("exams.set_exam");
                // Route::get('/{exam_id}', [CommonController::class, 'exam'])->name('exam');
                Route::any('/finish', [CommonController::class, 'examFinish'])->name('exam.finish')->middleware('remove.null_value');
            });
        });
        Route::fallback([CommonController::class, 'notfound'])->name('notfound');
    });
});

Route::post("upload_image_editor", [ApisController::class, 'upload_image_editor'])->name("api.upload_image_editor");
Route::post("qyestions_store", [ApisController::class, 'questions_store'])->name("front.questions.store");
Route::post("get_question_data", [ApisController::class, 'get_question_data'])->name("front.questions.get");
Route::post("remove_questionorsection_data", [ApisController::class, 'remove_questionorsection_data'])->name("front.questionsorsection.remove");
