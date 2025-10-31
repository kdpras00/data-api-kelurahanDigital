<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'thumbnail'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'name'        => 'required|string',
            'description' => 'required|string',
            'price'       => 'required|integer',
            'date'        => 'required|date',
            'time'        => 'required',
            'is_active'   => 'boolean',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail'   => 'thumbnail',
            'name'        => 'nama',
            'description' => 'deskripsi',
            'price'       => 'harga',
            'date'        => 'tanggal',
            'time'        => 'waktu',
            'is_active'   => 'aktif',
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
