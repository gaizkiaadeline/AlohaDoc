<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    protected $redirectRoute = 'landing';
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
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users,email',
            'jenis_kelamin' => [
                'required',
                Rule::in(['Pria', 'Wanita'])
            ],
            'role' => [
                'required',
                Rule::in(['admin', 'patient', 'doctor'])
            ],
            'telephone' => 'required|digits_between:6,20',
            'password' => 'required|min:5|max:255|confirmed'
        ];
    }
}