<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCourseRequest;
use App\Http\Requests\EnrollRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Group;
use App\Models\Module;
use App\Models\Project;
use App\Models\StudentGroup;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function addCourse(AddCourseRequest $request) 
    {
        $fields = $request->validated();

        $course = new Course();
        $course->name = $fields['name'];
        $course->description = $fields['description'];
        $course->category = $fields['category'];
        $course->price = $fields['price'];
        if(isset($fields['image']))
        {
            $image_name = time().rand(1000,9999).'.'.$fields['image']->extension();
            $course->image = $image_name;
            $request->image->move(public_path('images/courses'),$image_name);
        }
        $course->save();

        // default group
        $group = new Group();
        $group->name = 'default';
        $group->course = $course->id;
        $group->save();
        ////////////////

        $groups = $fields['groups'];
        foreach($groups as $g)
        {
            $group = new Group();
            $group->name = $g['name'];
            $group->course = $course->id;
            $group->save();
        }

        $modules = $fields['modules'];
        foreach($modules as $m)
        {
            $module = new Module();
            $module->name = $m['name'];
            $module->description = $m['description'];
            $module->duration = $m['duration'];
            $module->course = $course->id;
            $module->save();

            $teacher = new TeacherModule();
            $teacher->teacher = $m['teacher'];
            $teacher->module = $module->id;
            $teacher->save();

            $projects = $m['projects'];
            foreach($projects as $p)
            {
                $project = new Project();
                $project->name = $p['name'];
                $project->description = $p['description'];
                $project->module = $module->id;
                $project->save();
            }

            $chapters = $m['chapters'];
            foreach($chapters as $c)
            {
                $chapter = new Chapter();
                $chapter->name = $c['name'];
                $chapter->status = $c['status'];
                $chapter->module = $module->id;
                $chapter->save();
            }
        }
        
        return response(["message"=>"congrat baby"],201);
    }

    public function getCourses()
    {
        $result = [];
        $courses = Course::all();
        foreach($courses as $course)
        {
            array_push($result,[
                'id' => $course->id,
                'name' => $course->name,
                'price' => $course->price,
                'image' => $course->image
            ]);
        }
        return response($result,200);
    }

    public function getCourseDetails($id)
    {
        $course = Course::where('id',$id)->first();
        // if the requested course doesn't exist 
        if($course == null)
        {
            return response(['message'=>'this course does not exist'],422);
        }
        // get the modules of the current course
        $modules = Module::where('course',$id)->get();
        foreach($modules as $module)
        {
            $m = new Module();
            $m->id = $module->id;
            $m->name = $module->name;
            $m->description = $module->description;
            $m->duration = $module->duration;
            $m->teacher = DB::select('select users.full_name from teachers_modules join users on teachers_modules.teacher = users.id where teachers_modules.module = ?;',[$module->id])[0]->full_name;
            $projects = Project::where('module',$module->id)->get();
            $m->projects = $projects;

            $chapters = Chapter::where('module',$module->id)->get();
            $m->chapters = $chapters;
        }
        $course->modules = $m;

        $groups = Group::where('course',$id)->get();
        foreach($groups as $group)
        {
            $group->students_count = DB::select('select count(*) as count from students_groups where `group` = ?',[$group->id])[0]->count;
        }
        $course->groups = $groups;

        $teachers = DB::select('select users.id,users.full_name, users.image,count(*) as modules_count from users join teachers_modules on users.id = teachers_modules.teacher join modules on teachers_modules.module = modules.id where modules.course = ? group by users.id, users.full_name, users.image',[$id]);

        $course->teachers = $teachers;
        return response($course);
    }

    public function enroll(EnrollRequest $request)
    {
        $fields = $request->validated();

        $student = User::where('role',3)->where('id',$fields['student_id'])->first();
        if($student == null)
        {
            return response(['message'=> 'only the students are allowed to enroll the courses'],422);
        }
        $group = Group::where('course',$fields['course_id'])->where('name','default')->first();

        $student_group = new StudentGroup();
        $student_group->student = $fields['student_id'];
        $student_group->group = $group->id;
        $student_group->registration_date = now();
        $student_group->save();

        return response(['message'=>'you have enrolled successfully']);
    }
}
