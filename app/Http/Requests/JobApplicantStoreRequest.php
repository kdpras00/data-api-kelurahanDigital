<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicantStoreRequest extends FormRequest
{
    // 'job_vacancy_id',
    // 'user_id',
    // 'status',
    public function rules(): array
    {
        return [
            'job_vacancy_id' => 'required|exists:job_vacancies,id',
            'user_id'        => 'required|exists:users,id',
            'status'         => 'nullable|in:pending,approved,rejected',
        ];
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
