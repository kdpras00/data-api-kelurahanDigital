<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HeadOfFamilyStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'name'            => 'required|string',
            'email'           => 'required|string|email|unique:users',
            'password'        => 'required|string|min:8',
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,svg|max:2048',
            'identity_number' => 'required|integer|unique:head_of_families|digits:16',
            'gender'          => 'required|string|in:male,female',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string',
            'occupation'      => 'required|string',
            'marital_status'  => 'required|string|in:married,single',
        ];
    }

    public function attributes()
    {
        return [
            'name'            => 'Nama',
            'email'           => 'Email',
            'password'        => 'Kata Sandi',
            'profile_picture' => 'Foto Profil',
            'identity_number' => 'NIK',
            'gender'          => 'Jenis Kelamin',
            'phone_number'    => 'Nomor Telepon',
            'occupation'      => 'Pekerjaan',
            'marital_status'  => 'Status Perkawinan',
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
