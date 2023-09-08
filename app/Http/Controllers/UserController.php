<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Payment;
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
            'group' => 'required|integer|exists:groups,id',
            'course' => 'required|integer|exists:courses,id'
        ]);

        $user = User::where('id',$fields['student'])->first();
        if($user->role != 3)
        {
            return response(['message'=>'this user is not a student']);
        }

        // verify if the student really study in that course
        $old_student_group = DB::select('select sg.id, sg.group from `groups` g join students_groups sg on g.id = sg.group where g.course = ? and sg.student = ?',[$fields['course'], $fields['student']]);
        
        if(count($old_student_group)==0)
        {
            return response(['message'=>'this student don\'t study in this course'],422);
        }

        // chech if the old group and the new one belongs to the same course
        $old_group = Group::where('id',$old_student_group[0]->group)->first();
        $new_group = Group::where('id',$fields['group'])->first();
        if($old_group->course != $new_group->course)
        {
            return response(['message'=>'the change from a course to another can\'t be done'],422);
        }

        // change the group in student group
        $new_student_group = StudentGroup::where([['student','=',$fields['student']],['group','=',$old_group->id]])->first();
        $new_student_group->group = $fields['group'];
        $new_student_group->save();

        return response(['message'=>'student group have changed successfully']);
    }

    public function getStudents()
    {
        $students = User::where('role',3)->get(['id','full_name','email','phone_number']);
        foreach($students as $student)
        {
            $student_courses = DB::select('select c.id as course_id, c.name as course_name, g.id as group_id, g.name as group_name from users u join students_groups sg on u.id = sg.student join `groups` g on sg.group = g.id join courses c on c.id = g.course where u.id = ?;',[$student->id]);
            $courses = [];
            $projects = [];
            foreach($student_courses as $item)
            {
                $sessions = DB::select('select s.id , s.date, s.time from sessions s join presences p on s.id = p.`session` where p.student = ? and s.`group` = ?;',[$student->id,$item->group_id]);
                $pre_score = DB::select('select score from groups_projects gp join students_progresses sp on gp.id = sp.group_project where gp.`group` = ? and sp.student = ?;',[$item->group_id,$student->id]);
                $payment = Payment::where([['student','=',$student->id],['course','=',$item->course_id]])->first();
                $p = DB::select('select c.name as course, m.name as module, p.name as project, gp.deadline from courses c join modules m on c.id = m.course join projects p on p.module = m.id join groups_projects gp on p.id = gp.project where gp.group = ?',[$item->group_id]);
                $score = 0;
                $rest = null;
                if($pre_score!=null)
                {
                    $score = $pre_score[0]->score;
                }
                if(!is_null($payment))
                {
                    $rest = $payment->rest;
                }
                array_push($projects,...$p);
                array_push($courses,[
                    'id'=>$item->course_id,
                    'name'=>$item->course_name,
                    'group'=>$item->group_name,
                    'rest' => $rest,
                    'presence'=> $sessions,
                    'score'=> $score
                ]);                
                // array_push($result,[
                //     'full_name' => $item->user_full_name,
                //     'course' => $item->course_name,
                //     'presence' => $sessions,
                //     'score' => $score 
                // ]);
            }
            $student->courses = $courses;
            $student->projects = $projects;
        }
        
        return response($students);
    }

    public function getTeachers()
    {
        $teachers = DB::select('select id,full_name,email,phone_number from users where role = 2;');
        foreach($teachers as $teacher)
        {
            $courses = DB::select('select distinct c.id, c.name from teachers_modules tm join modules m on tm.module = m.id join courses c on m.course = c.id where tm.teacher = ?;',[$teacher->id]);
            foreach($courses as $course)
            {
                $modules = [];
                $ms = DB::select('select m.id, m.name from modules m join teachers_modules tm on m.id = tm.module where teacher = ? and m.course = ?',[$teacher->id, $course->id]);
                foreach($ms as $m)
                {
                    array_push($modules,$m);
                }
                $course->modules = $modules;
            }
            $consultations = DB::select('select c.id, c.subject, c.description,c.date ,cs.name as status,co.name as course, u1.full_name as student, c.id from consultations c join users u1 on c.student = u1.id join courses co on c.course = co.id join consultations_statuses cs on c.status = cs.id where c.teacher = ?;',[$teacher->id]);
            $teacher->courses = $courses;
            $teacher->consultations = $consultations;
        }
        return response($teachers);        
    }

    public function editProfile(Request $request)
    {
        $fields = $request->validate([
            'id' => 'required|integer',
            'full_name' => 'required|string|max:50',
            'phone_number' => 'required|string|unique:users,phone_number|max:20',
            'city' => 'required|string|max:20',
            'gender' => 'required|string|min:0|max:1',
            'image' => 'nullable|image'
        ]);
        $user = User::where('id',$fields['id'])->first();
        if(is_null($user))
        {
            return response(['message'=>'user not found'],422);
        }
        if(!isset($fields['image'])){
            $user = User::where('id',$fields['id'])->update([
                'first_name' => $fields['first_name'],
                'last_name' => $fields['last_name'],
                'phone_number' => $fields['phone_number'],
                'address' => $fields['address']
            ]);
        }else {
            // get the name of the old image
            $last_image = $user->image;
            $new_image = time().rand(1000,9999).'.'.$fields['image']->extension();
            // update the user information
            $user = User::where('id',$fields['id'])->update([
                'first_name' => $fields['first_name'],
                'last_name' => $fields['last_name'],
                'phone_number' => $fields['phone_number'],
                'address' => $fields['address'],
                'image' => $new_image
            ]);
            // add the new image
            $request->image->move(public_path('images/users'),$new_image);
            // get the path of the old image
            $image_path = public_path('images/users/'.$last_image);
            // delete the old image
            if(file_exists($image_path)){
                unlink($image_path);
            }
        }
        $response = [
            'message' => "profile updated successfully",
        ];
        return response($response,201);
    }

    public function changePassword(Request $request)
    {
        $fields = $request->validate([
            'user' => 'required|integer',
            'old_password' => 'required|string',
            'new_password' => 'required|string',
        ]);

        // check email
        $user = User::where('id',$fields['user'])->first();

        if(!Hash::check($fields['old_password'],$user->password))
        {
            return response([
                'message' => 'the old password is uncorrect'
            ],422);
        }

        $user = User::where('id',$fields['user'])->update([
            'password' => bcrypt($fields['new_password']),
        ]);

        return response([
            'message' => 'password changed successfully'
        ]);

    }

    public function getNewStudents() 
    {
        $students = DB::select('select u.id, u.full_name, u.phone_number, u.email, c.name as course, sg.registration_date from users u join students_groups sg on u.id = sg.student join `groups` g on sg.group = g.id join courses c on g.course = c.id where g.name = ?;',['default']);
        return response($students);
    }

    public function getTeacherStudents($course)
    {
        // $r1 = DB::select('select m.* from teachers_modules tm join modules m on tm.module = m.id where tm.teacher = ? and m.course = ?',[$teacher,$course]);
        $result = [];
        $groups = Group::where([['name', '<>','default'],['course','=',$course]])->get('id');
        foreach($groups as $group)
        {
            $students = DB::select('select u.full_name, u.image from users u join students_groups sg on u.id = sg.student where sg.group = ?',[$group->id]);
            foreach($students as $student)
            {
                array_push($result,$student);
            }
        }
        return response($result);
    }
}
