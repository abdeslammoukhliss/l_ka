<?php

namespace App\Http\Controllers;

use App\Models\GroupProject;
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
            $projects = DB::select('select p.name,gp.deadline from projects p join groups_projects gp on p.id = gp.project where gp.`group` = ?',[$group->id]);
            foreach($projects as $project)
            {
                array_push($result,[
                    'name' => $project->name,
                    'deadline' => $project->deadline
                ]);
            }
        }
        return response($result);
    }
}
