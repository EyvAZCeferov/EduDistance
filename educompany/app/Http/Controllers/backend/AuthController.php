<?php

namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login () {
        return view('backend.auth.login');
    }

    public function auth (Request $request) {

        try{
            $this->validate($request, [
                'email' => 'required|email|exists:admins',
                'password' => 'required'
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::guard('admins')->attempt($credentials, request()->has('rememberme'))) {
                session()->put("admin_id",Auth::guard('admins')->id());
                return redirect()->route('dashboard',['admin_id'=>Auth::guard('admins')->id()]);
            } else {
                return back()->withInput()->withErrors(['error' => 'Yanlış giriş']);
            }
        }catch(\Exception $e){
            return back()->withInput()->withErrors(['error' => 'Yanlış giriş']);
        }
    }

    public function logout () {
        session()->forget('admin_id');
        Auth::guard('admins')->logout();        
        return redirect()->route('admin.login');
    }
}
