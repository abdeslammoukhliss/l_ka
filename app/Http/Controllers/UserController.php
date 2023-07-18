<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function getStatistics()
    {
        $teachers_count = DB::select('select count(*) as count from users where role = 2')[0]->count;
        $students_count = DB::select('select count(*) as count from users where role = 3')[0]->count;
        $courses_count = DB::select('select count(*) as count from courses')[0]->count;
        $consultations = DB::select('select count(*) as count from consultations')[0]->count;
        $result = [
            'teachers' => $teachers_count,
            'students' => $students_count,
            'courses' => $courses_count,
            'consultations' => $consultations
        ];
        return response($result);
    }

    public function changeGroup(Request $request)
    {
        $fields = $request->validate([
            'student' => 'required|integer|exists:users,id',
            'group' => 'required|integer|exists:groups,id'
        ]);

        $user = User::where('id',$fields['student'])->first();
        if($user->role != 3)
        {
            return response(['message'=>'this user is not a student']);
        }
        $old_student_group = StudentGroup::where('student',$fields['student'])->first();

        // chech if the old group and the new one belongs to the same course
        $old_group = Group::where('id',$old_student_group->group)->first();
        $new_group = Group::where('id',$fields['group'])->first();
        if($old_group->course != $new_group->course)
        {
            return response(['message'=>'the change from a course to another is not possible']);
        }

        $student_group = new StudentGroup();
        $student_group->student = $user->id;
        $student_group->group = $fields['group'];
        $student_group->registration_date = $old_student_group->registration_date;
        $student_group->save();

        StudentGroup::where('id',$old_student_group->id)->delete();

        return response(['message'=>'student group have changed successfully']);
    }

    public function getStudents()
    {
        $student_courses = DB::select('select u.id as user_id, u.full_name as user_full_name, c.id as course_id, c.name as course_name, g.id as group_id from users u join students_groups sg on u.id = sg.student join `groups` g on sg.group = g.id join courses c on c.id = g.course;');
        $result = [];
        foreach($student_courses as $item)
        {
            $sessions = DB::select('select s.id ,s.date_and_time from sessions s join presences p on s.id = p.`session` where p.student = ? and s.`group` = ?;',[$item->user_id,$item->group_id]);
            $score = DB::select('select score from groups_projects gp join students_progresses sp on gp.id = sp.group_project where gp.`group` = ? and sp.student = ?;',[$item->group_id,$item->user_id])[0]->score;
            array_push($result,[
                'full_name' => $item->user_full_name,
                'course' => $item->course_name,
                'presence' => $sessions,
                'score' => $score 
            ]);
        }
        return response($result);
    }

    public function getTeachers()
    {
        $teachers = DB::select('select id,full_name from users where role = 2;');
        foreach($teachers as $teacher)
        {
            $courses = DB::select('select distinct c.id, c.name from teachers_modules tm join modules m on tm.module = m.id join courses c on m.course = c.id where tm.teacher = ?;',[$teacher->id]);
            $teacher->courses = $courses;
        }
        return response($teachers);        
    }
}
