<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustForecastRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'adjusted_quantity' => ['required', 'numeric', 'min:0'],
            'adjustment_note' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'adjusted_quantity.required' => 'Jumlah porsi hasil penyesuaian wajib diisi.',
            'adjusted_quantity.numeric' => 'Jumlah porsi harus berupa angka.',
            'adjusted_quantity.min' => 'Jumlah porsi tidak boleh negatif.',
            'adjustment_note.max' => 'Catatan penyesuaian maksimal 500 karakter.',
        ];
    }
}
