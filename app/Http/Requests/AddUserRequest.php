<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddUserRequest extends FormRequest
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
            'full_name' => 'required|string|max:50',
            'email' => 'required|string|unique:users,email|max:100',
            'password' => 'required|string|max:50',
            'phone_number' => 'required|string|unique:users,phone_number|min:10|max:15',
            'city' => 'required|string|max:50',
            'gender' => 'required|integer|min:1|max:2',
            'role' => 'required|integer|min:2|max:3',
            'image' => 'nullable|image'
        ];
    }
}
