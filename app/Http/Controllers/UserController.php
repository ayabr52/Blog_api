<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    //
    // Register
    public function register(Request $request){
        $request->validate([
            'name'=>['min:2','max:20','string','required'],
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','string','confirmed','min:4']
        ]);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);
        Mail::to($user->email)->send(new WelcomeMail());
        return response()->json([
            'message'=>'created user successfuly',
            'User'=> $user
        ], 201 );
    }
   ///  Loggin
public function login(Request $request){
      $request->validate([
            'email'=>['required','email'],
            'password'=>['required','string','min:4']
        ]);
        if(!Auth::attempt($request->only('email','password')))
        return response()->json(['message'=>'unvalid user'], 401);

        $user = User::where('email',$request->email)->FirstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
           return response()->json([
            'message'=>'Login successfuly',
            'User'=> $user,
            'Token'=>$token
        ], 201 );


}

      // logout
        public function logout(Request $request){
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message'=>'logout successfuly']);
        }











}
