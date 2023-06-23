<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TestRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'category' => 'required|integer',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'modules' => 'required|present',
            'groups' => 'required|present',
            
                'groups.*.name' => 'required|string',

                'modules.*.name' => 'required|string',
                'modules.*.description' => 'required|string',
                'modules.*.duration' => 'required|integer',
                'modules.*.projects' => 'required|present',
                'modules.*.chapters' => 'required|present',
            
                    'modules.*.projects.*.name' => 'required|string',
                    'modules.*.projects.*.description' => 'required|string',
                    'modules.*.projects.*.task' => 'required|integer',
                    
                    'modules.*.chapters.*.name' => 'required|string',
                    'modules.*.chapters.*.status' => 'required|integer',
            
        ];
    }
}
