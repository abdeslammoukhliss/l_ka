<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Http\Requests\SignInRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function addUser(AddUserRequest $request)
    {
        $fields = $request->validated();

        $registration_token = Str::random(64);

        $user = new User();
        $user->full_name = $fields['full_name'];
        $user->gender = $fields['gender'];
        $user->email = $fields['email'];
        $user->password = bcrypt($fields['password']);
        $user->registration_token = $registration_token;
        $user->phone_number = $fields['phone_number'];
        $user->city = $fields['city'];
        $user->role = $fields['role'];
        $user->save();

        // Mail::send('email_verification', ['id' => $user->id,'token' => $registration_token], function($message) use($request){
        //     $message->to($request->email);
        //     $message->subject('Email Verification Mail');
        // });

        return response(['message'=>'user inserted successfully'],201);
    }

    public function signIn(SignInRequest $request)
    {
        $fields = $request->validated();

        // check email
        $user = User::where('email',$fields['email'])->first();

        if($user == null){
            return response([
                'message' => 'the email is uncorrect'
            ],422);
        }
        if(!$user->hasVerifiedEmail()){
            return response([
                'message' => 'account verification is necessary'
            ],422);
        }
        if(!Hash::check($fields['password'],$user->password)) {
            return response([
                'message' => 'the password is uncorrect'
            ],422);
        }
        // $token = $user->createToken('myapptoken')->plainTextToken;

        return response([
            'user' => $user,
            // 'token' => $token
        ],200);
    }

    function verifyEmail($id,$token)
    {
        $user = User::where('id',$id)->first();
        if(is_null($user))
        {
            return view('email_verification_response')
                    ->with('message','this account is not exist');
        }
        if($user->email_verified_at != null)
        {
            return view('email_verification_response')
                    ->with('message','this account is already verified');
        }
        if($user->registration_token == $token)
        {
            User::where('id',$id)->update(['email_verified_at'=>now()]);
            return view('email_verification_response')
                    ->with('message','you have verified your account successfully');
        }
        return view('email_verification_response')
                ->with('message','annonymous error occured please contact the support');
    }

    function sendResetPasswordLink(Request $request)
    {
        $fields = $request->validate([
            'email'=>'required|string|email'
        ]);
        $user = User::where('email',$fields['email'])->first();
        if(is_null($user))
        {
            return response(['message'=>'this user does not exist']);
        }else if( $user->hasVerifiedEmail()){
            Mail::send('email_new_password', ['token' => $user->registration_token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Reset password link');
            });
            return response(['message'=>'reset link have been sent to the email successfully']);
        } else
        {
            return response(['message'=>'this account is not yet verified']);
        }

    }

    function newPassword(Request $request)
    {
        $fields = $request->validate([
            'token'=>'required|string',
            'password'=>'required|string',
            'repeat_password'=>'required|string'
        ]);

        if($fields['password']!=$fields['repeat_password'])
        {
            return view('new_password_form')->with(['message' => 'password_error', 'token' => $fields['token']]);
        }

        $user = User::where('registration_token',$fields['token'])->first();
        if(is_null($user))
        {
            return view('password_update_response')->with('message','this form is not valid anymore');
        } else
        {
            User::where('id',$user->id)->update([
                'password' => bcrypt($fields['password']),
                'registration_token' => Str::random(64)
            ]);
            return view('password_update_response')->with('message','password updated successfully');
        }
    }
}
