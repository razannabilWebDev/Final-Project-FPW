<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecordWasteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('record-waste') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'quantity' => ['required', 'numeric', 'decimal:0,3', 'min:0.001'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'ingredient_id.required' => 'Bahan wajib dipilih.',
            'ingredient_id.exists' => 'Bahan tidak ditemukan.',
            'quantity.required' => 'Jumlah susut/rusak wajib diisi.',
            'quantity.numeric' => 'Jumlah susut/rusak harus berupa angka.',
            'quantity.min' => 'Jumlah susut/rusak harus lebih dari nol.',
            'reason.required' => 'Alasan susut/rusak wajib diisi.',
        ];
    }
}
