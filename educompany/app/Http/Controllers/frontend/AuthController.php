<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Mail\ResetPassword;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use GeneaLabs\LaravelSocialiter\Facades\Socialiter;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if(isset($request->savethisurl) && !empty($request->savethisurl)){
                session()->put("savethisurl",$request->savethisurl);
            }
            return view('frontend.auth.login');
        } catch (\Exception $e) {
            return redirect()->back()->with("error",$e->getMessage());
        }
    }

    public function register(Request $request)
    {
        try {
            if(isset($request->savethisurl) && !empty($request->savethisurl)){
                session()->put("savethisurl",$request->savethisurl);
            }
            return view('frontend.auth.register');
        } catch (\Exception $e) {
            return redirect()->back()->with("error",$e->getMessage());
        }
    }

    public function authenticate(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required'
            ], [], [
                'email' => trans("additional.forms.email"),
                'password' => trans("additional.forms.password"),
            ]);

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            if (Auth::guard('users')->attempt($credentials)) {
                if(!empty(session()->get("savethisurl"))){
                    return redirect(session()->get("savethisurl"));
                }else{
                    return redirect()->route('user.profile');
                }
            } else {
                return redirect()->back()->with(['error' => trans('additional.messages.passwords_incorrect')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function registerSave(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email|string|unique:users,email',
                'password' => 'required|string|min:6',
                'name' => 'string|required|min:5',
                'phone' => 'string',
            ], [], [
                'email' => trans("additional.forms.email"),
                'password' => trans("additional.forms.password"),
                'name' => trans("additional.forms.name"),
                'phone' => trans("additional.forms.phone"),
            ]);

            $credentials = [];

            DB::transaction(function () use ($request, &$credentials) {

                $user = new User();
                $image = null;
                if ($request->hasFile('picture')) {
                    $image = image_upload($request->file("picture"), 'users');
                }
                $user->name = $request->name;
                $user->phone = $request->phone;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->user_type = $request->input("user_type") ?? 1;
                if (isset($image) && !empty($image)) {
                    $user->picture = $image;
                }
                $user->save();

                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                ];

            });

            if (Auth::guard('users')->attempt($credentials)) {
                if(!empty(session()->get("savethisurl"))){
                    return redirect(session()->get("savethisurl"));
                }else{
                    return redirect()->route('user.profile');
                }
            } else {
                return redirect()->back()->with(['error' => trans('additional.messages.passwords_incorrect')]);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function email()
    {
        return view('frontend.auth.email');
    }

    public function sendToken(Request $request)
    {
        if ($request->input("user_type") == 1) {
            $user = User::where('email', '=', request('email'))->first();
        } else {
            $user = User::where('phone', '=', request('phone'))->first();
        }

        if (!$user) {
            return redirect()->back()->with(['error' => 'İstifadəçi mövcud deyil']);
        }

        $token = Str::random(60);
        $code = createRandomCode();
        $link = route('reset', $token);

        if ($request->input("user_type") == 1) {
            Mail::to(request('email'))->send(new ResetPassword($link));
            DB::table('password_resets')->insert([
                'email' => request('email'),
                'token' => $token,
                'created_at' => Carbon::now(),
                'value' => $code,
            ]);
        } else {
            DB::table('password_resets')->insert([
                'phone' => request('phone'),
                'token' => $token,
                'created_at' => Carbon::now(),
                'value' => $code,
            ]);
        }
        return redirect()->route('login')->with(['success' => 'İsmarıc göndərildi.']);
    }

    public function reset($token)
    {
        $reset = DB::table('password_resets')->where('token', $token)->where('created_at', '>=', Carbon::now()->addHours(-1)->format('Y-m-d H:i:s'))->first();

        if (!$reset) {
            return redirect()->route('login')->with(['error' => 'Xəta!']);
        }

        return view('frontend.auth.reset', compact('token', 'reset'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $reset = DB::table('password_resets')->where('token', request('token'))->first();

        User::where('email', $reset->email)->update([
            'password' => Hash::make(request('password')),
        ]);

        DB::table('password_resets')->where('token', request('token'))->delete();
        return redirect()->route('login')->with(['success' => 'Şifrəniz yeniləndi']);
    }

    public function logout()
    {
        Auth::guard('users')->logout();
        return redirect()->route('login');
    }

    public function profile()
    {
        try {
            return view('frontend.auth.profile');
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
