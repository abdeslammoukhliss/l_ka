<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Session;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function addSession(Request $request)
    {
        $fields = $request->validate([
            'date' => 'required|date_format:format,Y-m-d',
            'time' => 'required|date_format:format,H:i',
            'duration' => 'required|integer',
            'group' => 'required|integer|exists:groups,id'
        ]);

        $session = new Session();
        $session->date = $fields['date'];
        $session->time = $fields['time'];
        $session->duration = $fields['duration'];
        $session->group = $fields['group'];
        $session->save();
        return response(['message'=>'the session have been created successfully']);
    }

    public function addPresence(Request $request)
    {
        $fields = $request->validate([
            'student' => 'required|integer|exists:users,id',
            'session' => 'required|integer|exists:sessions,id',
            'date' => 'required|date_format:format,Y-m-d',
            'time' => 'required|date_format:format,H:i',
        ]);

        $user = User::where('id',$fields['student'])->first();
        if($user->role != 3)
        {
            return response([
                'message' => 'this user is not a student'
            ],422);
        }
        $session = Session::where('id',$fields['session'])->first();
        if($session->date != $fields['date'])
        {
            return response(['message'=>'this session is passed or didn\'t reached'],422);
        }
        $student_group = StudentGroup::where('student',$fields['student'])->first();
        if($student_group->group != $session->group)
        {
            return response(['message'=>'you haven\'t the right to enter this session'],422);
        }
        $old_presence = Presence::where('student',$fields['student'])->where('session',$fields['session'])->first();
        if(!is_null($old_presence))
        {
            return response(['message'=>'you already pointed this session'],422);
        }
        $presence = new Presence();
        $presence->session = $fields['session'];
        $presence->student = $fields['student'];
        $presence->time = $fields['time'];
        $presence->save();

        return response(['message'=>'you have pointed successfully']);
    }

    public function getSessions()
    {
        return Session::get(['id','date','time','duration','group']);
    }
}
