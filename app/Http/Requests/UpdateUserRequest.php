<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->user()->role === 'admin';
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|string|min:3|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $this->id,
            'password' => 'sometimes|min:8',
            'role' => 'sometimes|in:admin,user'
        ];
    }
}
