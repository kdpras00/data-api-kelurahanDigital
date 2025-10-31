<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialAssistanceRecipientUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'social_assistance_id' => 'required|exists:social_assistances,id',
            'head_of_family_id'    => 'required|exists:head_of_families,id',
            'amount'               => 'required|integer',
            'reason'               => 'required|string',
            'bank'                 => 'required|string|in:mandiri,bca,bni,bri',
            'account_number'       => 'required',
            'proof'                => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status'               => 'nullable|string|in:pending,approved,rejected',
        ];
    }

    public function attributes()
    {
        return [
            'social_assistance_id' => 'Bantuan social',
            'head_of_family_id'    => 'Kepala Keluarga',
            'amount'               => 'Nominal',
            'reason'               => 'Alasan',
            'bank'                 => 'Bank',
            'account_number'       => 'Nomor Rekening',
            'proof'                => 'Bukti',
            'status'               => 'Status Pengajuan',
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
