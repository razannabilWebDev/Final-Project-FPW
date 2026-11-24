<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'type' => ['required', 'in:menu,manual'],
            'payment_method' => ['required', 'in:cash,transfer,qris'],
            'note' => ['nullable', 'string', 'max:1000'],
            'total_nominal' => ['required_if:type,manual', 'nullable', 'numeric', 'min:0'],
            'items' => ['required_if:type,menu', 'nullable', 'array', 'min:1'],
            'items.*.menu_id' => ['required_with:items', 'integer', 'exists:menus,id,is_active,1'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:0.001'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'items.required_if' => 'Minimal satu menu harus dipilih.',
            'items.*.menu_id.exists' => 'Menu yang dipilih tidak tersedia.',
            'total_nominal.required_if' => 'Nominal pendapatan wajib diisi untuk penjualan manual.',
        ];
    }
}
