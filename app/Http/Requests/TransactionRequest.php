<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'period_id' => ['required', 'integer', 'exists:periods,id'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'contribution_period_id' => ['nullable', 'integer', 'exists:contribution_periods,id'],
            'type' => ['required', 'string', 'in:IN,OUT'],
            'day' => ['nullable', 'integer', 'min:1', 'max:31'],
            'name' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }
}
