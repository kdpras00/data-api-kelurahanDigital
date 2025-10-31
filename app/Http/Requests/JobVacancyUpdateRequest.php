<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobVacancyUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'thumbnail'         => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'job_title'         => 'required|string',
            'description'       => 'required|string',
            'company_in_charge' => 'required|string',
            'start_date'        => 'required|date',
            'end_date'          => 'required|date',
            'salary'            => 'required|integer',
            'job_status'        => 'required|in:open,closed,filled',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail'         => 'thumbnail',
            'job_title'         => 'Posisi Pekerjaan',
            'description'       => 'Deskripsi Pekerjaan',
            'company_in_charge' => 'Perusahaan yang bertanggung jawab',
            'start_date'        => 'tanggal mulai',
            'end_date'          => 'tanggal selesai',
            'salary'            => 'gaji',
            'job_status'        => 'status pekerjaan',
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
