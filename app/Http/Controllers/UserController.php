<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function addUser(AddUserRequest $request)
    {
        $fields = $request->validated();

        $user = new User();
        $user->full_name = $fields['full_name'];
        $user->gender = $fields['gender'];
        $user->email = $fields['email'];
        $user->password = bcrypt($fields['password']);
        $user->phone_number = $fields['phone_number'];
        $user->city = $fields['city'];
        $user->role = $fields['role'];
        $user->save();

        return response(['message'=>'user inserted successfully'],201);
    }

    public function signIn(SignInRequest $request)
    {
        $validated = $request->validated();

        // check email
        // $user = User::where('email',$fields['email'])->first();

        // if($user == null){
        //     return response([
        //         'message' => 'the email is uncorrect'
        //     ]);
        // }
        // if(!Hash::check($fields['password'],$user->password)) {
        //     return response([
        //         'message' => 'the password is uncorrect'
        //     ]);
        // }
        // $token = $user->createToken('myapptoken')->plainTextToken;

        // return response([
        //     'user' => $user,
        //     'token' => $token
        // ]);
    }
}
