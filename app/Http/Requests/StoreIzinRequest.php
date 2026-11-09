<?php

namespace App\Http\Requests;

use App\Models\Siswa;
use App\Services\IzinService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreIzinRequest extends FormRequest
{
    /**
     * Otorisasi: Pastikan wali murid hanya dapat mengajukan izin untuk anaknya sendiri.
     */
    public function authorize(): bool
    {
        $siswaId = $this->input('siswa_id');

        if (! $siswaId) {
            return true; // Biarkan aturan required di rules() yang menangani errornya
        }

        return Siswa::where('id', $siswaId)
            ->where('user_id', $this->user()?->id)
            ->exists();
    }

    /**
     * Aturan validasi pengajuan izin.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedMimes = implode(',', config('izin.surat_sakit.allowed_mimes', ['pdf', 'jpg', 'jpeg', 'png']));
        $maxSizeKb = config('izin.surat_sakit.max_size_kb', 2048);
        $allowRetroactive = config('izin.allow_retroactive', false);

        $startDateRules = ['required', 'date'];
        if (! $allowRetroactive) {
            $startDateRules[] = 'after_or_equal:today';
        }

        return [
            'siswa_id' => ['required', 'integer', 'exists:siswa,id'],
            'jenis_izin' => ['required', 'in:sakit,izin'],
            'tanggal_mulai' => $startDateRules,
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'alasan' => ['required', 'string', 'min:5', 'max:1000'],
            'surat_sakit' => [
                'required_if:jenis_izin,sakit',
                'nullable',
                'file',
                "mimes:{$allowedMimes}",
                "max:{$maxSizeKb}",
            ],
        ];
    }

    /**
     * Pesan validasi dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $maxMb = round(config('izin.surat_sakit.max_size_kb', 2048) / 1024);

        return [
            'siswa_id.required' => 'Silakan pilih siswa yang akan diajukan perizinan.',
            'siswa_id.exists' => 'Data siswa yang dipilih tidak valid atau tidak terdaftar.',
            'jenis_izin.required' => 'Jenis perizinan wajib dipilih.',
            'jenis_izin.in' => 'Jenis izin harus berupa Sakit atau Izin.',
            'tanggal_mulai.required' => 'Tanggal mulai izin wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai tidak valid.',
            'tanggal_mulai.after_or_equal' => 'Tanggal mulai izin tidak boleh tanggal lampau.',
            'tanggal_selesai.required' => 'Tanggal selesai izin wajib diisi.',
            'tanggal_selesai.date' => 'Format tanggal selesai tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'alasan.required' => 'Alasan / keterangan izin wajib diisi.',
            'alasan.min' => 'Alasan izin minimal harus memuat 5 karakter.',
            'alasan.max' => 'Alasan izin maksimal 1000 karakter.',
            'surat_sakit.required_if' => 'Pengajuan izin sakit wajib melampirkan surat sakit / surat keterangan dokter.',
            'surat_sakit.file' => 'Surat sakit harus berupa berkas dokumen yang sah.',
            'surat_sakit.mimes' => 'Format berkas surat sakit harus berupa PDF, JPG, JPEG, atau PNG.',
            'surat_sakit.max' => "Ukuran berkas surat sakit tidak boleh lebih dari {$maxMb} MB.",
        ];
    }

    /**
     * Validasi lanjutan setelah aturan dasar lolos (aturan durasi izin sekolah FR-06).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $startDate = $this->input('tanggal_mulai');
            $endDate = $this->input('tanggal_selesai');

            if ($startDate && $endDate && strtotime($startDate) && strtotime($endDate)) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->startOfDay();

                if ($end->greaterThanOrEqualTo($start)) {
                    $izinService = app(IzinService::class);
                    $days = $izinService->calculateDuration($start, $end);
                    $maxDays = $izinService->getMaxDurationDays();

                    if ($izinService->isDurationExceeded($days)) {
                        $validator->errors()->add(
                            'tanggal_selesai',
                            "Durasi pengajuan izin ({$days} hari) melebihi batas maksimal sekolah ({$maxDays} hari). Silakan hubungi pihak sekolah untuk perizinan khusus."
                        );
                    }
                }
            }
        });
    }
}
