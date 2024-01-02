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
use Illuminate\Support\Facades\Session;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (isset($request->savethisurl) && !empty($request->savethisurl)) {
                Session::put("savethisurl", $request->savethisurl);
            }
            return view('frontend.auth.login');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    public function register(Request $request)
    {
        try {
            if (isset($request->savethisurl) && !empty($request->savethisurl))
                Session::put("savethisurl", $request->savethisurl);

            return view('frontend.auth.register');
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
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
                $userwith_subdomain = $request->input("subdomain")?? User::where('id', Auth::guard('users')->id())->whereNotNull("subdomain")->first()->subdomain;

                if (isset($userwith_subdomain) && !empty($userwith_subdomain)){
                    Session::put('subdomain', $userwith_subdomain ?? null);
                    create_dns_record($userwith_subdomain);
                }

                if (Session::has("savethisurl") && !empty(Session::get("savethisurl"))) {
                    return redirect(Session::get("savethisurl"));
                } else {
                    if (isset($userwith_subdomain) && !empty($userwith_subdomain)){
                        return redirect()->route('user.profile.subdomain');
                    }else{
                        return redirect()->route('user.profile');
                    }
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

            $user=User::where("phone",$request->phone)->orWhere("email",$request->email)->first();
            if(empty($user) && !isset($user->id)){
                $user = new User();
                $image = null;
                if ($request->hasFile('picture') && $request->file('picture')->isValid()) {
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
                $subdomain = Session::has("subdomain")? Session::get("subdomain") : $request->input("subdomain");
                if ($user->user_type == 2){
                    $subdomain = Str::slug($request->name);
                    create_dns_record($subdomain);
                }
                $user->subdomain = $subdomain;
                $user->save();

                $credentials = [
                    'email' => $request->email,
                    'password' => $request->password,
                ];

                if (Auth::guard('users')->attempt($credentials)) {
                    $userwith_subdomain = $request->input("subdomain")?? User::where('id', Auth::guard('users')->id())->whereNotNull("subdomain")->first()->subdomain;

                    if (isset($userwith_subdomain) && !empty($userwith_subdomain))
                        Session::put("subdomain", $userwith_subdomain);

                    if (!empty(Session::get("savethisurl"))) {
                        return redirect(Session::get("savethisurl"));
                    } else {
                        if (!empty($userwith_subdomain))
                            return redirect()->route('user.profile.subdomain', ['subdomain' => $userwith_subdomain]);
                        else
                            return redirect()->route('user.profile');
                    }
                } else {
                    return redirect()->back()->with(['error' => trans('additional.messages.passwords_incorrect')]);
                }
            }else{
                return redirect(route("login"))->with(['error' => trans('additional.messages.email_or_phone_mathced')]);
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
        session()->forget("subdomain");
        return redirect()->route('login');
    }

    public function profile()
    {
        try {
            return view('frontend.auth.profile');
        } catch (\Exception $e) {
            return redirect()->back()->with('error',$e->getMessage());
        }
    }
}
