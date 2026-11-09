<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class IzinService
{
    /**
     * Buat pengajuan izin baru.
     *
     * @param  array<string, mixed>  $data
     */
    public function buatIzin(User $waliMurid, array $data, ?UploadedFile $suratSakit = null): Izin
    {
        // Aturan bisnis: izin karena sakit wajib melampirkan surat sakit.
        if ($data['alasan'] === 'sakit' && $suratSakit === null) {
            throw ValidationException::withMessages([
                'surat_sakit' => 'Surat sakit wajib dilampirkan untuk izin karena sakit.',
            ]);
        }

        $suratSakitPath = null;

        if ($suratSakit !== null) {
            $suratSakitPath = $suratSakit->store('surat-sakit', 'surat_sakit');
        }

        return Izin::create([
            'siswa_id' => $data['siswa_id'],
            'user_id' => $waliMurid->id,
            'alasan' => $data['alasan'],
            'tanggal_mulai' => $data['tanggal_mulai'],
            'tanggal_selesai' => $data['tanggal_selesai'],
            'surat_sakit_path' => $suratSakitPath,
            'status' => 'menunggu',
            'catatan' => $data['catatan'] ?? null,
        ]);
    }
}
