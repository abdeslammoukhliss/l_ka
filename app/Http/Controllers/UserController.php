<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    function getStatistics()
    {
        $teachers_count = DB::select('select count(*) as count from users where role = 2')[0]->count;
        $students_count = DB::select('select count(*) as count from users where role = 3')[0]->count;
        $courses_count = DB::select('select count(*) as count from courses')[0]->count;
        $consultations = DB::select('select count(*) as count from consultations')[0]->count;
        $result = [
            'teachers'=>$teachers_count,
            'students'=>$students_count,
            'courses'=>$courses_count,
            'consultations'=>$consultations
        ];
        return response($result);
    }
}
