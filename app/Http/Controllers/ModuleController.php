<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Module;
use App\Models\Project;
use Illuminate\Http\Request;

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
            array_push($ms,$m);
        }
        return response($modules);
    
    }
}
