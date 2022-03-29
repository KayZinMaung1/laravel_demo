<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function user()
    {
        return Auth::user();
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);
        
        $email = $request->get('email');
        $password = $request->get('password');

        $user = User::whereEmail($email)->first();

        if (is_null($user)) {
            return response()->json(['message' => 'User does not exists.']);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json(['message' => 'Invalid Credentials.']);
        }

        $access_token = Auth::user()->createToken('authToken')->accessToken;
        return response(['user'=> Auth::user() , 'access_token'=>$access_token]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->password = Hash::make($request->get('password'));
        $user->save();
        return response()->json($user);
    }
}
