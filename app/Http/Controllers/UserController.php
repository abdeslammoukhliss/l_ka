<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function addUser(Request $request)
    {
        $fields = $request->validate([
            'full_name' => 'required|string|max:50',
            'email' => 'required|string|unique:users,email|max:100',
            'password' => 'required|string|max:50',
            'phone_number' => 'required|string|unique:users,phone_number|min:10|max:15',
            'city' => 'required|string|max:50',
            'gender' => 'required|integer',
            'role' => 'required|integer',
            'image' => 'nullable|image'
        ]);

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

    public function signIn(Request $request)
    {
        $fields = $request->validate([
            'email' => 'required|string|max:100',
            'password' => 'required|string|max:50',
        ]);

        // check email
        $user = User::where('email',$fields['email'])->first();

        if($user == null){
            return response([
                'message' => 'the email is uncorrect'
            ]);
        }
        if(!Hash::check($fields['password'],$user->password)) {
            return response([
                'message' => 'the password is uncorrect'
            ]);
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => $user,
            'token' => $token
        ]);
    }
}
