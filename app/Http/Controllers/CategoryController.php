<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function getCategories()
    {
        $result = [];
        $categories = Category::get();
        foreach($categories as $category)
        {
            array_push($result,[
                'id' => $category->id,
                'name' => $category->designation,
            ]);
        }
        return response($result);
    }
}
