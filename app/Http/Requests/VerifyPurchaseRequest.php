<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('verify-purchases') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'decision' => ['required', 'in:approved,rejected'],
            'rejection_note' => ['required_if:decision,rejected', 'nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'decision.required' => 'Keputusan verifikasi wajib dipilih.',
            'decision.in' => 'Keputusan verifikasi tidak valid.',
            'rejection_note.required_if' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
