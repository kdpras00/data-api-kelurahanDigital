<?php
namespace App\Http\Requests;

use App\Models\FamilyMember;
use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            //
            'name'            => 'required|string',
            'email'           => 'nullable|string|email|unique:users,email,' . FamilyMember::find($this->route('family_member'))->user_id,
            'password'        => 'nullable|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'identity_number' => 'required|integer|unique:family_members,identity_number,' . FamilyMember::find($this->route('family_member'))->id . ',id|digits:16',
            'gender'          => 'required|string|in:male,female',
            'date_of_birth'   => 'required|date',
            'phone_number'    => 'required|string',
            'occupation'      => 'required|string',
            'marital_status'  => 'required|string|in:married,single',
            'relation'        => 'required|string|in:husband,wife,child',
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
            'relation'        => 'Hubungan',
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
            'digits'       => ':attribute harus :digits digit.',
        ];
    }
}
