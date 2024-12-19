<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminRequest extends FormRequest
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
            'name' => 'required|min:3',
            'email' => 'required|email|unique:admins,email,' . $this->admin,
            'phone' => 'required|min:7|unique:admins,phone,' . $this->admin,
            'password' => ['nullable', 'confirmed',
                Password::min(6)->max(20)->mixedCase()->numbers()->symbols()->uncompromised(),
                Rule::requiredIf(request()->isMethod('POST')),
            ],

        ];
    }

}
