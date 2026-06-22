<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $parentId = $this->route('parent')->id;

        return [
            'first_name'                      => ['required', 'string', 'max:100'],
            'last_name'                        => ['required', 'string', 'max:100'],
            'middle_name'                      => ['nullable', 'string', 'max:100'],
            'phone'                            => ['required', 'string', 'max:20', Rule::unique('users', 'phone')->ignore($parentId)],
            'email'                            => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($parentId)],
            'students'                         => ['nullable', 'array'],
            'students.*.student_id'            => ['required_with:students', 'exists:students,id'],
            'students.*.relationship'          => ['required_with:students', Rule::in(['Father', 'Mother', 'Guardian', 'Uncle', 'Aunt', 'Sibling', 'Others'])],
            'students.*.is_primary_contact'    => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required'                        => 'Phone number is required (used as primary contact).',
            'phone.unique'                          => 'This phone number is already registered to another account.',
            'students.*.student_id.required_with'   => 'Please select a student for each row.',
            'students.*.relationship.required_with' => 'Please select the relationship for each student.',
        ];
    }
}
