<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\LanguagesController;
use App\Http\Controllers\Admin\MainCategory;
use App\Http\Controllers\Admin\DashBoard;
use App\Http\Controllers\Admin\VendorsController;
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

Route::get('logout',  function () {
    Auth::guard('admin')->logout();        
    return Redirect('admin/login');    
    })->name('admin.logout');
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
        Route::get('status/{id}',[LanguagesController::class,'statusLanguages'])->name('admin.Languages.status');

    });

    ##################### End Languages Routes #########################

    ##################### Begin MainCategory Routes ####################

      Route::group(['prefix' => 'maincategories'], function () {
        Route::get('/',[MainCategory::class,'index'])->name('admin.maincategories');
        Route::get('create',[MainCategory::class,'createCategory'])->name('admin.maincategories.create');
        Route::post('store',[MainCategory::class,'storeCategory'])->name('admin.maincategories.store');
        Route::get('edit/{id}',[MainCategory::class,'editCategory'])->name('admin.maincategories.edit');
        Route::post('update/{id}',[MainCategory::class,'updateCategory'])->name('admin.maincategories.update');
        Route::get('status/{id}',[MainCategory::class,'changeStatus'])->name('admin.maincategories.status');
        Route::get('delete/{id}',[MainCategory::class,'deleteCategory'])->name('admin.maincategories.delete');

    });

    ##################### End MainCategory Routes ########################

    ##################### Begin Vendors Routes ###########################

        Route::group(['prefix' => 'vendors'], function () {
            Route::get('/',[VendorsController::class,'index'])->name('admin.vendors');
            Route::get('create',[VendorsController::class,'createvendors'])->name('admin.vendors.create');
            Route::post('store',[VendorsController::class,'storevendors'])->name('admin.vendors.store');
            Route::get('edit/{id}',[VendorsController::class,'editvendors'])->name('admin.vendors.edit');
            Route::post('update/{id}',[VendorsController::class,'updatevendors'])->name('admin.vendors.update');
            Route::get('status/{id}',[VendorsController::class,'changeStatus'])->name('admin.vendors.status');
            Route::get('delete/{id}',[VendorsController::class,'deletevendors'])->name('admin.vendors.delete');
    
        });
    
    ##################### End Vendors Routes #############################


});
Auth::routes();

Route::group(['namespace'=>'Admin','middleware'=>'guest:admin'], function () {
    Route::get('login',[LoginController::class,'getLogin'])->name('get.admin.login');
    Route::post('login',[LoginController::class,'Login'])->name('admin.login');

});



