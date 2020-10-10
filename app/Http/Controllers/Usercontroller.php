<?php

namespace App\Http\Controllers;

use App\User;
use App\Contact;
use App\Bottle;
use Illuminate\Http\Request;
use Laravel\Passport\Bridge\AccessToken;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            return response()->json(['error' => 'Email & Password is incorrect'], 422);
        }
        // $user = User::select('*')->where('email', 'arehmans@live.com')->first();
        // $success['token'] = $user->createToken('MyApp')->accessToken;
        // return response()->json(['success' => $success]);
    }
    public function details()
    {
        $user = Auth::user();
        $NumberofSupply = Bottle::where('u_id', $user->id)->where('created_at', '>=', Carbon::now()->subdays(30))->get();
        $NumberofSupplyCount = count($NumberofSupply);
        ///////////////////////////////////////////
        $Monthlyincome = Bottle::where('u_id', $user->id)->where('status', 'Yes')->where('created_at', '>=', Carbon::now()->subdays(30))->get();
        $newdata = $Monthlyincome->map(function ($item) {
            $amount = $item->num_of_bottle * $item->price;
            return  $amount;
        });
        $MonthlyincomeCount = $newdata->sum();
        ///////////////////////////////////////////
        $TotalBill = Bottle::where('u_id', $user->id)->where('status', 'No')->where('created_at', '>=', Carbon::now()->subdays(30))->get();
        $TotalBill1 = $TotalBill->map(function ($item) {
            $amount = $item->num_of_bottle * $item->price;
            return  $amount;
        });
        $TotalBillCount = $TotalBill1->sum();
        ///////////////////////////////////////////
        return response()->json([
            'success' => $user,
            'NumberofSupply' => $NumberofSupplyCount,
            'Monthlyincome' => $MonthlyincomeCount,
            'TotalBill' => $TotalBillCount,
        ]);
    }
    public function test()
    {
        return response()->json(['success' => "abcd"]);
    }
}
