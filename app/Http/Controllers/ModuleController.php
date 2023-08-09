<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function getAllModules()
    {
        return Module::get(['id','name','description','duration','course']);
    }
}
