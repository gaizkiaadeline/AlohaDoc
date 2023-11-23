<?php

namespace App\Http\Requests;

use App\Rules\AuthenticatePassword;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|exists:users,email|email',
            'password' => ['required', 'min:5', 'max:255', new AuthenticatePassword],
            'role' => 'required'
        ];
    }
}
