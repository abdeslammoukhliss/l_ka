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
            // 'date' => 'required|date_format:format,Y-m-d',
            // 'time' => 'required|date_format:format,H:i',
        ]);

        $user = User::where('id',$fields['student'])->first();
        if($user->role != 3)
        {
            return response([
                'message' => 'this user is not a student'
            ],422);
        }
        $session = Session::where('id',$fields['session'])->first();
        if(Carbon::now()->format('Y-m-d') != $session->date)
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
        $presence->time = Carbon::now()->format('H:i');
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

    public function getSessionsByDate($date)
    {
        $sessions = [];
        $users = [];
        if (strtotime($date) === false) {
            return response(['message' => 'you didn\'t insert a real date'],422);
        }
        $sessions = DB::select('select s.id, g.name as `group`, c.name as course from sessions s join `groups` g on s.group = g.id join courses c on g.course = c.id where s.date = ?',[$date]);
        foreach($sessions as $session)
        {
            $users = DB::select('select u.id as user_id, u.full_name, p.time from users u join presences p on u.id = p.student where p.session = ?',[$session->id]);
            $session->users = $users;
        }
        return $sessions;
    }

    public function deleteSession(Request $request)
    {
        $fields = $request->validate([
            'session_id' => 'required|integer|exists:sessions,id'
        ]);
        $presences = Presence::where('session',$fields['session_id'])->get();
        if(sizeof($presences)>0)
        {
            return response(['message'=>'this session can\'t be deleted'],422);
        }
        Session::where('id',$fields['session_id'])->delete();
        return response(['message'=>'you have deleted this session successfully']);
    }

    public function getSessionStudent($session_id)
    {
        $session = Session::where('id',$session_id)->first();
        if(is_null($session))
        {
            return response(['message'=>'this session does not exist'],422);
        }
        $students = DB::select('select u.id,u.full_name from users u join students_groups sg on u.id = sg.student where sg.group = ?',[$session->group]);
        return response($students);
    }
}
