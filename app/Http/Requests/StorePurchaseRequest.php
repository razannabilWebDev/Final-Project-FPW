<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create-purchases') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'purchase_date' => ['required', 'date'],
            'supplier_name' => ['nullable', 'string', 'max:255'],
            'receipt' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'decimal:0,3', 'min:0.001'],
            'items.*.unit' => ['required', 'string', 'max:20'],
            'items.*.unit_cost' => ['required', 'numeric', 'decimal:0,4', 'min:0.0001'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'purchase_date.required' => 'Tanggal pembelian wajib diisi.',
            'receipt.mimes' => 'Bukti nota harus berupa JPG atau PNG.',
            'receipt.max' => 'Ukuran bukti nota maksimal 5 MB.',
            'items.required' => 'Minimal satu item pembelian wajib diisi.',
            'items.min' => 'Minimal satu item pembelian wajib diisi.',
            'items.*.ingredient_id.required' => 'Bahan baku wajib dipilih.',
            'items.*.quantity.required' => 'Kuantitas wajib diisi.',
            'items.*.quantity.min' => 'Kuantitas harus lebih dari nol.',
            'items.*.unit.required' => 'Satuan wajib diisi.',
            'items.*.unit_cost.required' => 'Harga satuan wajib diisi.',
            'items.*.unit_cost.min' => 'Harga satuan harus lebih dari nol.',
        ];
    }
}
