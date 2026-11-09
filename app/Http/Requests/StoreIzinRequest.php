<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use Illuminate\Foundation\Http\FormRequest;

class StoreIzinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null || ! $user->hasRole('wali_murid')) {
            return false;
        }

        $siswa = Siswa::find($this->input('siswa_id'));

        return $siswa !== null && $siswa->user_id === $user->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tipeDiizinkan = implode(',', (array) config('izinku.surat_sakit.tipe_diizinkan'));

        return [
            'siswa_id' => ['required', 'exists:siswa,id'],
            'alasan' => ['required', 'in:sakit,izin,lainnya'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'surat_sakit' => [
                'nullable',
                'file',
                'mimes:'.$tipeDiizinkan,
                'max:'.(int) config('izinku.surat_sakit.ukuran_maksimal_kb'),
            ],
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
            'surat_sakit.mimes' => 'Format surat sakit harus: '.implode(', ', (array) config('izinku.surat_sakit.tipe_diizinkan')).'.',
            'surat_sakit.max' => 'Ukuran surat sakit maksimal '.config('izinku.surat_sakit.ukuran_maksimal_kb').' KB.',
        ];
    }
}
