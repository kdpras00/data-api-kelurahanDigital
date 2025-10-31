<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            //
            'thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'name'         => 'required|string',
            'category'     => 'required|in:staple,cash,subsidized fuel,health',
            'amount'       => 'required',
            'provider'     => 'required|string',
            'description'  => 'required',
            'is_available' => 'required|boolean',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail'    => 'Thumbnail',
            'name'         => 'Nama',
            'category'     => 'Jenis Bantuan',
            'amount'       => 'Jumlah Bantuan',
            'provider'     => 'Pemberi Bantuan',
            'description'  => 'Deskripsi',
            'is_available' => 'Ketersediaan',
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
