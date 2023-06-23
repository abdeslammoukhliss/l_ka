<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function tester(TestRequest $request)
    {
        $validated = $request->validated();
        return response($validated,201);
    }
}
