<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\AccessToken;
use Illuminate\Support\Facades\Auth;

class Usercontroller extends Controller
{
    //
    public function register1(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'email|required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user =  User::create($validatedData);
        $token = $user->createToken('MyApp')->accessToken;
        return response(['user' => $user, 'access_token' => $token]);
    }
    public function register(Request $request)
    {
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        return response()->json(['success' => $success]);
    }
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $user = Auth::user();
            $token = $user->createToken('MyApp')->accessToken;
            return response()->json(['token' => $token, 'user' => $user]);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
        // $user = User::select('*')->where('email', 'arehmans@live.com')->first();
        // $success['token'] = $user->createToken('MyApp')->accessToken;
        // return response()->json(['success' => $success]);
    }
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user]);
    }
}
