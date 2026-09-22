<?php

namespace App\Http\Controllers;

use App\Support\PersistentLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            $user = PersistentLogin::userFromRequest(request());
            if ($user) {
                PersistentLogin::bind($user);
                PersistentLogin::queue($user);
            }
        }

        if (Auth::check()) {
            return redirect()->route("admin.index");
        }

        return redirect("login");
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'captcha' => 'required|captcha',
        ],[
            'username.required' => 'Silahkan isi username terlebih dahulu.',
            'password.required' => 'Silahkan isi password terlebih dahulu.',
            'captcha.required' => 'Silahkan isi captcha terlebih dahulu.',
            'captcha.captcha'   => 'Captcha tidak sesuai.',
        ]);

        $demoUser = 'admin';
        $demoPassword = 'admkeu90';

        $credentials = $request->only(['username', 'password']);

        if ($credentials['username'] === $demoUser && $credentials['password'] === $demoPassword) {
            session(['user' => ['username' => $credentials['username']]]);
            return redirect()->route('admin.index');
        }

        return back()->withErrors([
            'username' => 'Username salah, silahkan periksa kembali.',
            'password' => 'Password salah, silahkan periksa kembali.',
        ])->withInput();
    }

    // Handle logout logic
    public function logout()
    {
        PersistentLogin::forget();
        Session::forget('user');
        return redirect()->route('login');
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_src()]);
    }
}
