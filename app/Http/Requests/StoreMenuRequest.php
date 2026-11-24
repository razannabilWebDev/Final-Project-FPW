<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-menus') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'selling_price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama menu wajib diisi.',
            'name.max' => 'Nama menu maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1000 karakter.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.numeric' => 'Harga jual harus berupa angka.',
            'selling_price.decimal' => 'Harga jual maksimal dua angka di belakang koma.',
            'selling_price.min' => 'Harga jual tidak boleh negatif.',
            'image.image' => 'File foto menu harus berupa gambar.',
            'image.mimes' => 'Format foto menu harus JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran foto menu maksimal 2 MB.',
        ];
    }
}
