<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use App\Models\GroupProject;
use App\Models\Session;
use App\Models\StudentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function getCourseGroups($course)
    {
        $c = Course::where('id',$course)->first();
        if(is_null($c))
        {
            return response(['message'=>'this course does not exist anymore']);
        }
        $groups = Group::where('course',$course)->get();
        $result = [];
        foreach($groups as $group)
        {
            if($group->name!='default')
            {
                array_push($result,$group);
            }
        }
        return response($result);
    }

    public function getAllGroups()
    {
        $groups = Group::get(['id','name','course']);
        foreach($groups as $group)
        {
            $group->students_count = DB::select('select count(*) as count from students_groups where `group` = ?',[$group->id])[0]->count;
        }
        return $groups;
    }

    public function addGroup(Request $request)
    {
        $fields = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'name' => 'required|string',
        ]);

        Group::create([
            'course' => $fields['course_id'],
            'name' => $fields['name'],
        ]);

        return response(['message'=>'you have created a new group successfully']);
    }

    public function editGroup(Request $request)
    {
        $fields = $request->validate([
            'group_id' => 'required|integer|exists:groups,id',
            'name' => 'required|string',
        ]);

        Group::where('id',$fields['group_id'])->update([
            'name' => $fields['name'],
        ]);

        return response(['message'=>'you have updated the group successfully']);
    }

    public function deleteGroup(Request $request)
    {
        $fields = $request->validate([
            'group_id' => 'required|integer|exists:groups,id'
        ]);
        $sessions = Session::where('group',$fields['group_id'])->get();
        $students_groups = StudentGroup::where('group',$fields['group_id'])->get();
        $groups_projects = GroupProject::where('gorup',$fields['group_id'])->get();
        if(sizeOf($sessions) == 0 && sizeOf($students_groups) == 0 && sizeOf($groups_projects) == 0) 
        {
            Group::where('id',$fields['group_id'])->delete();
            return response(['message'=>'you have delete the group successfully']);
        }
        return response(['message'=>'you can\'t delete this group because other fields depend on it'],422);
    }

}
