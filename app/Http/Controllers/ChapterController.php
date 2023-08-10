<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use Illuminate\Http\Request;

class ChapterController extends Controller
{
    public function getAllChapters()
    {
        return Chapter::get(['id','name','module']);
    }
}
