<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function addConsultation(Request $request)
    {
        $fields = $request->validate([
            'student' => 'required|integer|exists:users,id',
            'course' => 'required|integer|exists:courses,id',
            'subject' => 'required|string',
            'description' => 'required|string',
            'date' => 'required|date',
        ]);

        $user = User::where('id', $fields['student'])->first();
        if ($user->role != 3) {
            return response(['message' => 'this user is not a student'], 422);
        }

        $students_group = DB::select('select * from students_groups sg join `groups` g on sg.group = g.id where g.course = ? and sg.student = ?;', [$fields['course'], $fields['student']]);

        if (sizeof($students_group) == 0) {
            return response(['message' => 'you are not enrolled in that course'], 422);
        }

        $consultation = new Consultation();
        $consultation->student = $fields['student'];
        $consultation->course = $fields['course'];
        $consultation->subject = $fields['subject'];
        $consultation->description = $fields['description'];
        $consultation->date = $fields['date'];
        $consultation->status = 1;
        $consultation->save();

        return response(['message' => 'created successfully']);
    }

    public function getConsultations()
    {
        $consultations = DB::select('select c.id, c.subject, c.description,c.date ,cs.name as status,co.name as course, u1.full_name as student, u2.full_name as teacher, c.id from consultations c join users u1 on c.student = u1.id join users u2 on c.teacher = u2.id join courses co on c.course = co.id join consultations_statuses cs on c.status = cs.id;');
        return $consultations;
    }

    public function getStudentConsultations($student)
    {
        $user = User::where('id', $student)->first();
        if (is_null($user)) 
        {
            return response(['message' => 'this user does not exist'], 422);
        }
        if ($user->role != 3) 
        {
            return response(['message' => 'this user is not a student']);
        }
        $consultations = DB::select(
            'select co.name as course, c.subject, c.description, cs.name as status, c.date from consultations_statuses cs join consultations c on cs.id = c.status join courses co on c.course = co.id where c.student = ?;',
            [$student]
        );
        return $consultations;
    }

    public function editConsultation(Request $request)
    {
        $fields = $request->validate([
            'consultation' => 'required|integer|exists:consultations,id',
            'decision' => 'required|integer|min:0|max:1',
            'teacher' => 'nullable|integer|exists:users,id'
        ]);

        $consultation = Consultation::where('id',$fields['consultation'])->first();
        if($fields['decision'] == 0)
        {
            $consultation->delete();
            return response(['message'=>'you have rejected the consultation successfully'],422);
        } else 
        {
            $consultation->status = 2;
            if(!isset($fields['teacher']))
            {
                return response(['message'=> 'you didn\'t insert the teacher'],422);
            }else 
            {
                $teacher = User::where('id',$fields['teacher'])->first();
                if($teacher->role!=2)
                {
                    return response(['message'=>'this user is not a teacher'],422);
                }
                $consultation->teacher = $fields['teacher'];
                $consultation->save();
                return response(['message'=>'you have confirmed the consultation successfully'],422);
            }
        }
    }
}
