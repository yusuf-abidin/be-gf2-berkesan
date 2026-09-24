<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResidentRequest extends FormRequest
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
        return [
            'coordinator_id' => ['nullable', 'integer', 'exists:coordinators,id'],
            'name' => ['required', 'string', 'max:255'],
            'block' => ['nullable', 'string', 'max:255'],
            'house_number' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'min:5', 'max:255',
                'regex:/^(08|628)[1-9][0-9]{6,13}$/',
            ],
        ];
    }
}
