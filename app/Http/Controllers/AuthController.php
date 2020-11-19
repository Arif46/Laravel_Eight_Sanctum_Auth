<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Validator;
use Hash;
use Auth;

class AuthController extends Controller
{
    /**
    * user Register
    */
    public function register(Request $req)
    {
        $validator=Validator::make($req->all(),[
               'name'=>'required',
               'email'=>'required|email',
               'password'=>'required', 
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 400);
        }

        $user = New User();
        $user->name=$req->name;
        $user->email=$req->email;
        $user->password=Hash::make($req->password);
        $user->save();

        return response()->json([
            'status_code'=>200,
            'message'=>'User Register Successfully'
        ]);
    }

    /**
    * user login
    */
    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),[

            'email'=>'required|email',
            'password'=>'required',
        ]);

        if($validator->fails()){
            return response()->json([$validator->errors()],400);
        }

        $credentials=request(['email','password']);
        if (!Auth::attempt($credentials))
        {
            return response()->json([
                'status_code'=>500,
                 'message'=>'Unauthorized'
            ]);

        }

        $user = User::where('email', $request->email)->first();
        $tokenResult=$user->createToken('authToken')->plainTextToken;

        return response()->json([
             'status_code'=>200,
              'user'=>$user,
             'token'=>$tokenResult
        ]);

    }

    /**
     *User logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
           'status_code'=>200,
            'message'=>'Token Deleted Sucessfully!'
        ]);       
    }


}
