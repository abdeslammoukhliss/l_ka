<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Group;
use Illuminate\Http\Request;

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
}
