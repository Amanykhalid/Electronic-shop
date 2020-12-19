<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\SessionGuard;

class LoginController extends Controller
{
    //

   public function getLogin()
   {
       return view('admin\Auth\login');

   }

   public function Login(LoginRequest $req)
   {
    $remember_me = $req->has('remember_me') ? true : false;

    if (Auth::guard('admin')->attempt(['email' => $req->input("email"), 'password' => $req->input("password")], $remember_me)) {
        // notify()->success('تم الدخول بنجاح  ');
        return redirect() -> route('admin.dashboard');
    }
   // notify()->error('خطا في البيانات  برجاء المجاولة مجدا ');
    return redirect()->back()->with(['error' => 'هناك خطا بالبيانات']);

   }

}
