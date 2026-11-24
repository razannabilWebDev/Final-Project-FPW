<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use App\Models\Menu;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-recipes') ?? false;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'menu_id' => ['required', 'integer', 'exists:menus,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'integer', 'distinct', 'exists:ingredients,id'],
            'items.*.quantity' => ['required', 'numeric', 'decimal:0,3', 'min:0.001'],
            'items.*.unit' => ['required', 'string', 'max:20'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'menu_id.required' => 'Menu wajib dipilih.',
            'menu_id.exists' => 'Menu tidak ditemukan.',
            'items.required' => 'Minimal satu bahan harus diisi.',
            'items.min' => 'Minimal satu bahan harus diisi.',
            'items.*.ingredient_id.required' => 'Bahan wajib dipilih.',
            'items.*.ingredient_id.distinct' => 'Bahan tidak boleh duplikat.',
            'items.*.ingredient_id.exists' => 'Bahan tidak ditemukan.',
            'items.*.quantity.required' => 'Takaran wajib diisi.',
            'items.*.quantity.numeric' => 'Takaran harus berupa angka.',
            'items.*.quantity.decimal' => 'Takaran maksimal tiga angka di belakang koma.',
            'items.*.quantity.min' => 'Takaran harus lebih dari nol.',
            'items.*.unit.required' => 'Satuan wajib diisi.',
        ];
    }

    /** @return array<int, callable> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            $menu = Menu::find($this->input('menu_id'));

            if ($menu !== null && ! $menu->is_active) {
                $validator->errors()->add('menu_id', 'Menu tidak aktif.');
            }

            foreach ($this->input('items', []) as $index => $item) {
                if (! is_array($item) || ! isset($item['ingredient_id'])) {
                    continue;
                }

                $ingredient = Ingredient::find($item['ingredient_id']);

                if ($ingredient === null) {
                    continue;
                }

                if (! $ingredient->is_active) {
                    $validator->errors()->add("items.$index.ingredient_id", 'Bahan tidak aktif.');
                }

                if (strtolower((string) ($item['unit'] ?? '')) !== strtolower($ingredient->base_unit)) {
                    $validator->errors()->add(
                        "items.$index.unit",
                        "Satuan harus {$ingredient->base_unit} (satuan dasar bahan).",
                    );
                }
            }
        }];
    }
}
