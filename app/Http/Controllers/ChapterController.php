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

    public function getModuleChapters($module)
    {
        return Chapter::where('module',$module)->get(['id','name','module']);
    }

    public function addChapter(Request $request)
    {
        $fields = $request->validate([
            'name' => 'required|string',
            'module' => 'required|integer|exists:modules,id',
        ]);

        Chapter::create([
            'name' => $fields['name'],
            'module' => $fields['module'],
            'status' => 0
        ]);

        return response(['message'=>'you have created a new chapter successfully']);
    }

    public function editChapter(Request $request)
    {
        $fields = $request->validate([
            'chapter_id' => 'required|integer|exists:chapters,id',
            'name' => 'required|string',
            'status' => 'required|integer|min:0|max:1',
        ]);

        Chapter::where('id',$fields['chapter_id'])->update([
            'name' => $fields['name'],
            'description' => $fields['description']
        ]);

        return response(['message'=>'you have updated the chapter successfully']);
    }

    public function deleteChapter(Request $request) {
        $fields = $request->validate([
            'chapter_id' => 'integer|required|exists:chapters,id'
        ]);

        Chapter::where('id',$fields['chapter_id'])->delete();

        return response(['message'=>'you have deleted this chapter successfully']);
    }
}
