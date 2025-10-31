<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'thumbnail'         => 'nullable|image',
            'name'              => 'required|string',
            'about'             => 'required|string',
            'headman'           => 'required|string',
            'people'            => 'required|integer',
            'agricultural_area' => 'required',
            'total_area'        => 'required',
            'latitude'          => 'nullable|numeric|between:-90,90',
            'longitude'         => 'nullable|numeric|between:-180,180',
            'images'            => 'nullable|array',
            'images.*'          => 'required|image|mimes:png,jpg|max:2048',
        ];
    }

    public function attributes()
    {
        return [
            'thumbnail'         => 'thumbnail',
            'name'              => 'Nama',
            'about'             => 'Deskripsi',
            'headman'           => 'kepala Desa',
            'people'            => 'Jumlah Penduduk',
            'agricultural_area' => 'luas pertanian',
            'total_area'        => 'Luas Total',
            'latitude'          => 'Latitude',
            'longitude'         => 'Longitude',
            'images'            => 'Gambar',
        ];
    }
}
