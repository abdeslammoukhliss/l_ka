<?php

namespace App\Http\Controllers;

use App\Http\Requests\TestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/get_users",
     *     tags={"users"},
     *     summary="first swagger test",
     *     description="Multiple status values can be provided with comma separated string",
     *     operationId="findPetsByStatus",
     *     deprecated=false,
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid status value"
     *     ),
     * )
     */
    public function getUsers()
    {
        return DB::select('select * from users;');
    }

    public function tester(TestRequest $request)
    {
        $validated = $request->validated();
        return response($validated,201);
    }
}
