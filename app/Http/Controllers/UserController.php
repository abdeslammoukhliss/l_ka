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
}
