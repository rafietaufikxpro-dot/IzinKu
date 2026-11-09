<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccIzinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'aksi' => ['required', 'in:disetujui,ditolak'],
            'catatan_penolakan' => ['required_if:aksi,ditolak', 'nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'catatan_penolakan.required_if' => 'Alasan penolakan wajib diisi.',
        ];
    }
}
