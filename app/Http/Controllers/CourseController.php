<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCourseRequest;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Group;
use App\Models\Module;
use App\Models\Project;
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
}
