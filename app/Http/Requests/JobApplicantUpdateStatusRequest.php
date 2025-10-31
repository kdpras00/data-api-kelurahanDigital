<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobApplicantUpdateStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => 'required|in:pending,approved,rejected',
        ];
    }

    public function attributes()
    {
        return [
            'status' => 'status',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute harus diisi.',
            'in'       => ':attribute harus salah satu dari :values.',
        ];
    }
}

