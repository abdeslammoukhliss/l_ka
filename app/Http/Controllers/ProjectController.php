<?php

namespace App\Http\Controllers;

use App\Models\GroupProject;
use App\Models\Module;
use App\Models\Project;
use App\Models\StudentGroup;
use App\Models\StudentProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function assignProject(Request $request)
    {
        $fields = $request->validate([
            'project' => 'required|integer|exists:projects,id',
            'group' => 'required|integer|exists:groups,id',
            'deadline' => 'required|date'
        ]);

        $group_project = new GroupProject();
        $group_project->project = $fields['project'];
        $group_project->group = $fields['group'];
        $group_project->deadline = $fields['deadline'];
        $group_project->affected_date = now();
        $group_project->save();

        $students_groups = StudentGroup::where('group',$fields['group'])->get();
        foreach($students_groups as $single_student)
        {
            $student_progress = new StudentProgress();
            $student_progress->student = $single_student->student;
            $student_progress->group_project = $group_project->id;
            $student_progress->save();
        }
        return response(['message' => 'group assigned successfully']);
    }

    public function getStudentProjects($student)
    {
        $result = [];
        $groups = DB::select('select g.id from students_groups sg join `groups` g on sg.group = g.id where sg.student = ?;',[$student]);
        foreach($groups as $group)
        {
            $projects = DB::select('select p.id, p.name,p.module, p.description,gp.deadline, gp.affected_date from projects p join groups_projects gp on p.id = gp.project where gp.`group` = ?',[$group->id]);
            foreach($projects as $project)
            {
                // $module = DB::select('select name from modules where id = ?;',[$project->module])[0]->name;
                $course = DB::select('select c.id from courses c join modules m on c.id = m.course where m.id = ?;',[$project->module])[0]->id;
                array_push($result,[
                    'id' => $project->id,
                    'name' => $project->name,
                    'deadline' => $project->deadline,
                    'affected_date' => $project->affected_date,
                    'description' => $project->description,
                    'course' => $course,
                    'moduleId' => $project->module
                ]);
            }
        }
        return response($result);
    }

    public function getAllProjects()
    {
        $result = [];
        $modules = Module::all();
        foreach($modules as $module)
        {
            $projects = Project::where('module',$module->id)->get(['id','name','description','module']);
            foreach ($projects as $key => $project) {
                $project->module=$module->name;
                $project->moduleId=$module->id;
            }
            array_push($result,...$projects);
        }
        return $result;
    }

    public function getCourseProjects($course)
    {
        $result = [];
        $modules = Module::where('course',$course)->get('id');
        foreach($modules as $module)
        {
            $projects = Project::where('module',$module->id)->get(['id','name','description','module']);
            array_push($result,...$projects);
        }
        return $result;
    }

    public function getTeacherProjects($teacher, $course)
    {
        return DB::select('select p.name as project, gp.group, gp.affected_date, gp.deadline from teachers_modules tm join modules m on tm.module = m.id join projects p on m.id = p.module join groups_projects gp on p.id = gp.project where tm.teacher = ? and m.course = ?;',[$teacher,$course]);
    }

    public function addProject(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'module' => 'required|integer|exists:modules,id',
        ]);

        Project::create([
            'name' => $fields['name'],
            'description' => $fields['description'],
            'module' => $fields['module']
        ]);

        return response(['message'=>'you have created a new project successfully']);
    }

    public function editProject(Request $request)
    {
        $fields = $request->validate([
            'project_id' => 'required|integer|exists:projects,id',
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        $project = Project::where('id',$fields['project_id'])->first();
        $project->update([
            'name' => $fields['name'],
            'description' => $fields['description']
        ]);

        return response(['message'=>'you have updated the project successfully']);
    }

    public function deleteProject(Request $request) 
    {
        $fields = $request->validate([
            'project_id' => 'required|integer|exists:projects,id'
        ]);

        $group_project = GroupProject::where('project',$fields['project_id'])->get();
        if(sizeof($group_project) == 0)
        {
            Project::where('id',$fields['project_id'])->delete();

            return response(['message'=>'you have delete this project successfully']);
        }
        return response(['message'=>'you can\'t delete this project, other fields depend on it'],422);

    }
}
