<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'         => 'required|string|max:100',
            'last_name'          => 'required|string|max:100',
            'middle_name'        => 'nullable|string|max:100',
            'email'              => 'nullable|email|max:150|unique:users,email',
            'phone'              => 'nullable|string|max:20|unique:users,phone',
            'photo'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'date_of_birth'      => 'nullable|date|before:today',
            'gender'             => 'required|in:Male,Female',
            'religion'           => 'nullable|in:Christianity,Islam,Others',
            'state_of_origin'    => 'nullable|string|max:50',
            'lga'                => 'nullable|string|max:50',
            'home_address'       => 'nullable|string|max:500',
            'blood_group'        => 'nullable|in:A+,A-,B+,B-,O+,O-,AB+,AB-',
            'genotype'           => 'nullable|in:AA,AS,SS,AC,SC',
            'medical_conditions' => 'nullable|string|max:1000',
            'previous_school'    => 'nullable|string|max:150',
            'admission_date'     => 'nullable|date',
            'class_arm_id'       => 'nullable|exists:class_arms,id',
        ];
    }
}
