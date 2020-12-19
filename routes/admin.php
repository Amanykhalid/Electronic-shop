<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\LanguagesController;
use App\Http\Controllers\Admin\DashBoard;
use Illuminate\Support\Facades\Auth;




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
define('PAGINATION_COUNT',10);

Route::group(['namespace'=>'Admin','middleware'=>'auth:admin'], function () {
    Route::get('/', [DashBoard::class,'index'])->name('admin.dashboard');

    ##################### Begin Languages Routes #######################

    Route::group(['prefix' => 'languages'], function () {
        Route::get('/',[LanguagesController::class,'index'])->name('admin.Languages');
        Route::get('create',[LanguagesController::class,'createLanguages'])->name('admin.Languages.create');
        Route::post('store',[LanguagesController::class,'storeLanguages'])->name('admin.Languages.store');
        Route::get('edit/{id}',[LanguagesController::class,'editLanguages'])->name('admin.Languages.edit');
        Route::post('update/{id}',[LanguagesController::class,'updateLanguages'])->name('admin.Languages.update');
        Route::get('delete/{id}',[LanguagesController::class,'deleteLanguages'])->name('admin.Languages.delete');

    });

    ##################### End Languages Routes #########################


});
Auth::routes();

Route::group(['namespace'=>'Admin','middleware'=>'guest:admin'], function () {
    Route::get('login',[LoginController::class,'getLogin'])->name('get.admin.login');
    Route::post('login',[LoginController::class,'Login'])->name('admin.login');

});



