<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'first_name'               => ['required', 'string', 'max:100'],
            'last_name'                => ['required', 'string', 'max:100'],
            'middle_name'              => ['nullable', 'string', 'max:100'],
            'email'                    => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone'                    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'photo'                    => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            'date_of_birth'            => ['nullable', 'date', 'before:today'],
            'gender'                   => ['required', Rule::in(['Male', 'Female'])],

            'qualification'            => ['required', 'string', 'max:150'],
            'specialization'           => ['nullable', 'string', 'max:150'],
            'employment_date'          => ['required', 'date'],
            'employment_type'          => ['required', Rule::in(['Full-Time', 'Part-Time', 'Contract'])],
            'address'                  => ['required', 'string'],

            'next_of_kin_name'         => ['nullable', 'string', 'max:150'],
            'next_of_kin_phone'        => ['nullable', 'string', 'max:20'],
            'next_of_kin_relationship' => ['nullable', 'string', 'max:50'],
        ];
    }
}
