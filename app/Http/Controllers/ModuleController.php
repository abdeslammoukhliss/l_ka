<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Module;
use App\Models\Project;
use App\Models\TeacherModule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ModuleController extends Controller
{
    public function getAllModules()
    {
        $modules = Module::get(['id','name','description','duration','course']);
        $ms = [];
        foreach($modules as $m)
        {
            $projects = Project::where('module',$m->id)->get('id');
            $m->projects = $projects;

            $chapters = Chapter::where('module',$m->id)->get('id');
            $m->chapters = $chapters;

            $teacher = DB::select('select u.full_name from users u join teachers_modules tm on u.id = tm.teacher where tm.module = ?;',[$m->id])[0];
            $m->teacher = $teacher->full_name;

            array_push($ms,$m);
        }
        return response($modules);
    }

    public function addModule(Request $request) 
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'course_id' => 'required|integer|exists:courses,id',
            'teacher' => 'required|integer|exists:users,id'
        ]);

        $teacher = User::where('id',$fields['teacher'])->first();
        if($teacher->role!=2)
        {
            return response(['message'=>'you can\'t assign an admin or a user instead of a teacher'],422);
        }

        $module = new Module();
        $module->name = $fields['name'];
        $module->description = $fields['description'];
        $module->duration = $fields['duration'];
        $module->course = $fields['course_id'];
        $module->save();

        $teacher_module = new TeacherModule();
        $teacher_module->module = $module->id;
        $teacher_module->teacher = $teacher->id;
        $teacher_module->save();

        return response(['message'=>'the module is inserted successfully']);
    }

    public function editModule(Request $request) 
    {
        $fields = $request->validate([
            'module_id' => 'required|integer|exists:modules,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'duration' => 'required|integer',
            'teacher' => 'required|integer|exists:users,id'
        ]);

        $teacher = User::where('id',$fields['teacher'])->first();
        if($teacher->role!=2)
        {
            return response(['message'=>'you can\'t assign an admin or a user instead of a teacher'],422);
        }

        $module = Module::where('id',$fields['module_id'])->first();
        $module->name = $fields['name'];
        $module->description = $fields['description'];
        $module->duration = $fields['duration'];
        $module->save();

        $teacher_module = TeacherModule::where('module',$module->id)->first();
        $teacher_module->teacher = $teacher->id;
        $teacher_module->save();
        
        return response(['message'=>'module updated successfully']);
    }
}
