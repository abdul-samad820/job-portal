<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('superadmin')->check()
            && auth('superadmin')->user()->role === 'super_admin';
    }

    public function rules(): array
    {
        return [
            'company_name' => 'required|string|max:255',
            'contact_number' => 'nullable|digits_between:10,12',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1080',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|confirmed|min:8',
        ];
    }

    public function messages(): array
    {
        return [
               'company_name.required' => 'Company name is required.',
            'email.unique' => 'An admin with this email already exists.',
            'password.confirmed' => 'Passwords do not match.',
            'password.min' => 'Password must be at least 8 characters.',
        ];
    }
}