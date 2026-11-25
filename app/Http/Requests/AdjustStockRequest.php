<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update-stock-physical') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'physical_quantity' => ['required', 'numeric', 'decimal:0,3', 'min:0'],
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
            'physical_quantity.required' => 'Jumlah stok fisik wajib diisi.',
            'physical_quantity.numeric' => 'Jumlah stok fisik harus berupa angka.',
            'physical_quantity.min' => 'Jumlah stok fisik tidak boleh negatif.',
            'reason.required' => 'Alasan penyesuaian wajib diisi.',
        ];
    }
}
