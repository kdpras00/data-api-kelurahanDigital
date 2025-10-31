<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class JobApplicantUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'job_vacancy_id' => 'nullable|exists:job_vacancies,id',
            'user_id'        => 'nullable|exists:users,id',
            'status'         => 'nullable|in:pending,approved,rejected',
        ];
    }
    
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->job_vacancy_id) && empty($this->user_id) && empty($this->status)) {
                $validator->errors()->add('any', 'At least one field must be provided.');
            }
        });
    }

    public function attributes()
    {
        return [
            'job_vacancy_id' => 'loker',
            'user_id'        => 'user',
            'status'         => 'status',
        ];
    }

    public function messages()
    {
        return [
            'required'     => ':attribute harus diisi.',
            'string'       => ':attribute harus berupa string.',
            'max'          => ':attribute maksimal :max karakter.',
            'min'          => ':attribute minimal :min karakter.',
            'unique'       => ':attribute sudah terdaftar.',
            'email'        => ':attribute harus berupa email.',
            'image'        => ':attribute harus berupa gambar.',
            'exists'       => ':attribute tidak ditemukan.',
            'integer'      => ':attribute harus berupa angka.',
            'array'        => ':attribute harus berupa array.',
            'mimes'        => ':attribute harus berupa gambar dengan ekstensi jpeg, png, jpg, atau svg.',
            'max:2048'     => ':attribute maksimal 2MB.',
            'unique:users' => ':attribute sudah terdaftar.',
            'in'           => ':attribute harus salah satu dari :values.',
        ];
    }
}
