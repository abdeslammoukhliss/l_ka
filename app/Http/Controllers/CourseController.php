<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCourseRequest;
use App\Http\Requests\EnrollRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Disponibility;
use App\Models\Group;
use App\Models\Module;
use App\Models\Payment;
use App\Models\Project;
use App\Models\StudentGroup;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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
        
        return response(["message"=>"course inserte succefully"],201);
    }

    public function addCourseOnly(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'category' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

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

        return response(['message'=>'course had been added successfully']);
    }

    public function editCourse(Request $request)
    {
        $fields = $request->validate([
            'course_id'=>'required|integer|exists:courses,id',
            'category'=>'required|integer|exists:categories,id',
            'name'=>'required|string',
            'description'=>'required|string',
            'price'=>'required|numeric',
        ]);

        $course = Course::where('id',$fields['course_id'])->first();

        $course->category = $fields['category'];
        $course->name = $fields['name'];
        $course->description = $fields['description'];
        $course->price = $fields['price'];
        $course->save();

        return response(['message'=>'you have updated the course successfully']);
    }

    public function getCourses()
    {
        $result = [];
        $courses = Course::all();
        foreach($courses as $course)
        {
            $modules = Module::where('course',$course->id)->get();
            $count = 0;
            $duration = 0;
            foreach($modules as $module)
            {
                $count++;
                $duration = $duration + $module->duration;
            }
            array_push($result,[
                'id' => $course->id,
                'name' => $course->name,
                'price' => $course->price,
                'image' => $course->image,
                'modules' => $count,
                'duration' => $duration
            ]);
        }
        // modules number
        // course duration
        return response($result,200);
    }

    public function getCoursesWithDetails()
    {
        $courses = Course::get(['id','name','description','image','category','price']);
        foreach($courses as $course)
        {
            // get the modules of the current course
            $ms = Module::where('course',$course->id)->get();
            $duration=$ms->avg('duration');
            $modules = [];
            foreach($ms as $m)
            {
                array_push($modules,$m->id);
            }
            
            $course["duration"]=$duration;
            $course->modules = $modules;
            $gs = Group::where('course',$course->id)->get();
            $groups = [];
            foreach($gs as $g)
            {
                if($g->name == 'default'){
                    continue;
                }
                array_push($groups,$g->id);
            }
            $course->groups=$groups; 
            $ts = DB::select('select distinct tm.teacher as id from teachers_modules tm join modules m on tm.module = m.id where m.course = ?',[$course->id]);
            $teachers = [];
            foreach($ts as $t)
            {
                array_push($teachers,$t->id);
            }
             $course->teachers = $teachers;
             $pr = Project::whereIn('module',$modules)->get('id'); // select('select distinct pr.id as id from projects pr where pr.module in ?',[$modules]);//
             $projects = [];
             foreach($pr as $p)
             {
                 array_push($projects,$p->id);
             }
              $course->projects = $projects;
            
            $chapters = [];
            $ch = Chapter::whereIn('module',$modules)->get('id');
            foreach($ch as $c){
                array_push($chapters, $c->id);
            }
            $course->chapters = $chapters;
        }
        return response($courses);
    }

    public function getTeacherCourses($teacher)
    {
        $value = User::where('id',$teacher)->first();
        if(is_null($value))
        {
            return response(['message'=>'this teacher does not exist']);
        }else if($value->role != 2)
        {
            return response(['message'=>'this user is not a teacher']);
        }
        $result = [];
        $courses = [];
        $teachers_modules = DB::select('select m.name,m.course from teachers_modules tm join modules m on tm.module = m.id where tm.teacher = ?;',[$teacher]);
        foreach($teachers_modules as $module)
        {
            $course = Course::where('id',$module->course)->first();
            if(!in_array($course,$courses))
            {
                array_push($courses,$course);
            }
        }
        foreach($courses as $course)
        {
            $modules = Module::where('course',$course->id)->get();
            $modules_count = sizeOf($modules);
            $projects_count = 0;
            foreach($modules as $module)
            {
                $projects = Project::where('module',$module->id)->get();
                $projects_count = sizeOf($projects);
            }
            $sessions_count = DB::select('select count(*) as c from sessions s join `groups` g on s.group = g.id where g.course = ?',[$course->id])[0]->c;
            array_push($result,[
                'id' => $course->id,
                'name' => $course->name,
                'price' => $course->price,
                'image' => $course->image,
                'modules' => $modules_count,
                'projects' => $projects_count,
                'sessions' => $sessions_count
            ]);
        }
        // modules number
        // course duration
        return response($result,200);
    }

    public function getStudentCourses($student)
    {
        $value = User::where('id',$student)->first();
        if(is_null($value))
        {
            return response(['message'=>'this student does not exist'],422);
        }else if($value->role != 3)
        {
            return response(['message'=>'this user is not a student'],422);
        }
        $result = [];
        $courses = [];
        // get the student courses IDs
        $student_courses = DB::select("select g.course from users u join students_groups sg on u.id = sg.student join `groups` g on sg.`group` = g.id where u.id = ?;",[$student]);
        foreach($student_courses as $course)
        {
            $course = Course::where('id',$course->course)->first();
            if(!in_array($course,$courses))
            {
                array_push($courses,$course);
            }
        }
        foreach($courses as $course)
        {
            $modules = Module::where('course',$course->id)->get();
            $modules_count = 0;
            $duration = 0;
            foreach($modules as $module)
            {
                $modules_count++;
                $duration = $duration + $module->duration;
            }
            $group = DB::select('select g.id from students_groups sg join `groups` g on sg.group = g.id where sg.student = ? and g.course = ?;',[$student,$course->id])[0];
            $presence = DB::select('select count(*) as count from sessions s join presences p on s.id = p.`session` where p.student = ? and s.`group` = ?;',[$student,$group->id])[0]->count;
            $s = DB::select('select score from groups_projects gp join students_progresses sp on gp.id = sp.group_project where gp.`group` = ? and sp.student = ?;',[$group->id,$student]);
            $score=0;
            if(sizeOf($s)>0){
                $score = $s[0]->score;
            }
            array_push($result,[
                'id' => $course->id,
                'name' => $course->name,
                'group' => $group->id,
                // 'price' => $course->price,
                // 'image' => $course->image,
                //'modules' => $modules_count,
                'duration' => $duration,
                'presence' => $presence,
                'score' => $score
            ]);
        }
        // modules number
        // course duration
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
        $ms = [];
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
            array_push($ms,$m);
        }
        $course->modules = $ms;

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

    public function getCourseDetailsForStudent($course_id,$student_id)
    {
        $course = Course::where('id',$course_id)->first();
        // if the requested course doesn't exist
        if($course == null)
        {
            return response(['message'=>'this course does not exist'],422);
        }
        $student = User::where('id',$student_id)->first();
        if(is_null($student))
        {
            return response(['message'=>'this student does not exist'],422);
        }else if($student->role != 3)
        {
            return response(['message'=>'this person is not a student'],422);
        }
        $student_group = DB::select('select `group` from students_groups where student = ?',[$student_id])[0]->group;
        $ms = [];
        // get the modules of the current course
        $modules = Module::where('course',$course_id)->get();
        foreach($modules as $module)
        {
            $m = new Module();
            $m->id = $module->id;
            $m->name = $module->name;
            $m->description = $module->description;
            $m->duration = $module->duration;
            $m->teacher = DB::select('select users.full_name from teachers_modules join users on teachers_modules.teacher = users.id where teachers_modules.module = ?;',[$module->id])[0]->full_name;

            $chapters = Chapter::where('module',$module->id)->get();
            $m->chapters = $chapters;
            array_push($ms,$m);
        }
        $course->modules = $ms;
        $projects = DB::select('select p.name,gp.deadline from projects p join groups_projects gp on p.id = gp.project where gp.`group` = ?;',[$student_group]);
        $course->projects = $projects;

        $groups = Group::where('course',$course_id)->get();
        foreach($groups as $group)
        {
            $group->students_count = DB::select('select count(*) as count from students_groups where `group` = ?',[$group->id])[0]->count;
        }
        $course->groups = $groups;

        $teachers = DB::select('select users.id,users.full_name, users.image,count(*) as modules_count from users join teachers_modules on users.id = teachers_modules.teacher join modules on teachers_modules.module = modules.id where modules.course = ? group by users.id, users.full_name, users.image',[$course_id]);

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

        $old_student_group = DB::select('select sg.id, sg.group from `groups` g join students_groups sg on g.id = sg.group where g.course = ? and sg.student = ?',[$fields['course_id'], $fields['student_id']]);
        
        if(count($old_student_group)>0)
        {
            return response(['message'=>'you are already enrolled in this course'],422);
        }

        $group = Group::where('course',$fields['course_id'])->where('name','default')->first();

        $student_group = new StudentGroup();
        $student_group->student = $fields['student_id'];
        $student_group->study_method = $fields['study_method'];
        $student_group->group = $group->id;
        $student_group->registration_date = now();
        $student_group->save();

        $disponibilities = $fields['disponibilities'];
        foreach($disponibilities as $d)
        {
            $disponibility = new Disponibility();
            $disponibility->student_group = $student_group->id;
            $disponibility->shift = $d['shift'];
            $disponibility->day = $d['day'];
            $disponibility->save();
        }

        $course = Course::where('id',$fields['course_id'])->first();
        $payment = new Payment();
        $payment->total = 0;
        $payment->rest = $course->price;
        $payment->course = $course->id;
        $payment->student = $fields['student_id'];
        $payment->save();

        return response(['message'=>'you have enrolled successfully']);
    }

    public function deleteCourse(Request $request)
    {
        $fields = $request->validate([
            'course_id' => 'required|integer|exists:courses,id'
        ]);

        $modules = Module::where('course',$fields['course_id'])->get();
        $groups = Group::where('course',$fields['course_id'])->get();

        if(sizeof($modules) > 0 || sizeof($groups) > 0)
        {
            return response(['message' => 'this course can\'t be deleted'],422);
        }

        Course::where('id',$fields['course_id'])->delete();
        return response(['message'=>'course has been deleted successfully']);
    }
}
