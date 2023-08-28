<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Module;
use App\Models\Project;
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
}
