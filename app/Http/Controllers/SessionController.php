<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Session;
use App\Models\StudentGroup;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $sessions = DB::select('select s.id, c.name as course, s.date, s.time, s.duration, g.name as `group` from sessions s join `groups` g on s.group = g.id join courses c on c.id = g.course;');
        return $sessions;
    }
    
    public function getStudentSessions($student_id)
    {
        $student = User::where('id',$student_id)->first();
        if(is_null($student))
        {
            return response(['message'=>'this student does not exist'],422);
        }
        if($student->role != 3)
        {
            return response(['message'=>'this user is not a student'],422);
        }
        $sessions = DB::select('select s.id, c.name as course, s.date, s.time, s.duration, g.name as `group` from sessions s join `groups` g on s.group = g.id join courses c on c.id = g.course join students_groups sg on g.id = sg.group where sg.student = 4;');
        return $sessions;
    }
}
