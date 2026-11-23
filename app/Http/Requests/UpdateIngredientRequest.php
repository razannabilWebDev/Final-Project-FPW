<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-ingredients') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('ingredients', 'name')->ignore($this->route('ingredient'))],
            'base_unit' => ['required', 'string', 'max:20'],
            'minimum_stock' => ['required', 'numeric', 'decimal:0,3', 'min:0'],
            'average_cost' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama bahan wajib diisi.',
            'name.unique' => 'Nama bahan sudah digunakan.',
            'base_unit.required' => 'Satuan dasar wajib diisi.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.min' => 'Stok minimum tidak boleh negatif.',
            'average_cost.required' => 'Harga modal wajib diisi.',
            'average_cost.min' => 'Harga modal tidak boleh negatif.',
        ];
    }
}
