<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedMinutes = [
            60,             // 1 Jam
            60 * 6,         // 6 Jam (360)
            60 * 24,        // 1 Hari (1440)
            60 * 24 * 7,    // 7 Hari (10080)
            60 * 24 * 30,   // 30 Hari (43200)
        ];

        return [
            'code' => ['required', 'string', 'max:255', 'unique:registration_codes,code', 'regex:/^[a-zA-Z0-9_-]+$/',],
            'expires_at' => ['required', 'integer', Rule::in($allowedMinutes)],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'code.required' => 'Kode registrasi wajib diisi.',
            'code.unique' => 'Kode registrasi sudah digunakan.',
            'code.regex' => 'Kode registrasi hanya boleh mengandung huruf, angka, dan tanda "-" atau "_".',
            'expires_at.required' => 'Waktu kedaluwarsa wajib diisi.',
            'expires_at.in' => 'Waktu kedaluwarsa harus berupa angka dan memiliki nilai yang valid.',
            'usage_limit.min' => 'Jumlah penggunaan minimal harus 1.',
            'usage_limit.integer' => 'Jumlah penggunaan harus berupa angka.',
        ];
    }
}
