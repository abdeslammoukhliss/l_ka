<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCourseRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;
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
            'category' => 'required|integer|exists:categories,id',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'groups' => 'required|present',
                'groups.*.name' => 'required|string',
            'modules' => 'nullable|present',
                'modules.*.name' => 'required|string',
                'modules.*.description' => 'required|string',
                'modules.*.duration' => 'required|integer',
                'modules.*.teacher' => 'required|integer|exists:users,id',
                'modules.*.projects' => 'required|present',
                    'modules.*.projects.*.name' => 'required|string',
                    'modules.*.projects.*.description' => 'required|string',
                'modules.*.chapters' => 'required|present',
                    'modules.*.chapters.*.name' => 'required|string',
                    'modules.*.chapters.*.status' => 'required|integer',
            
        ];
    }
}
