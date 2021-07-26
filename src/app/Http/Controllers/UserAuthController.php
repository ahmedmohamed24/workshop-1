<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAuthController extends Controller
{
    public function Register()
    {
        return \view('user.register');
    }

    public function doRegister(UserRegisterRequest $request)
    {
        $request->password = Hash::make($request->password);
        $user = User::create($request->validated());
        Auth::login($user);

        return \redirect('/')->with('user', $user);
    }

    public function login()
    {
        return \view('user.login');
    }

    public function doLogin(UserLoginRequest $request)
    {
        $isLogged = Auth::attempt($request->validated());
        if ($isLogged) {
            return \redirect('/')->with('message', 'success');
        }

        return \back()->withErrors(['message' => 'These credentials is invalid!']);
    }
}
