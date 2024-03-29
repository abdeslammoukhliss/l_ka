<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollRequest extends FormRequest
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
            'course_id' => 'required|integer|exists:courses,id',
            'student_id' => 'required|integer|exists:users,id',
            'study_method' => 'required|integer|exists:study_methods,id',
            'disponibilities' => 'required|present',
                'disponibilities.*.day' => 'required|integer',
                'disponibilities.*.shift' => 'required|integer|exists:shifts,id',
        ];
    }
}
