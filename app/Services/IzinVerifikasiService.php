<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class IzinVerifikasiService
{
    /**
     * Proses ACC izin: menyetujui atau menolak.
     *
     * Aturan bisnis:
     * - Izin belum boleh diproses sebelum dikonfirmasi wali murid.
     * - Status ACC hanya: menunggu, disetujui, ditolak.
     * - Setiap perubahan status mencatat siapa dan kapan.
     */
    public function accIzin(User $guru, Izin $izin, string $aksi, ?string $alasanPenolakan = null): Izin
    {
        if ($izin->confirmed_at === null) {
            throw ValidationException::withMessages([
                'izin' => 'Izin belum dikonfirmasi oleh wali murid sehingga belum dapat diproses.',
            ]);
        }

        if ($izin->status !== 'menunggu') {
            throw ValidationException::withMessages([
                'izin' => 'Izin ini sudah diproses sebelumnya.',
            ]);
        }

        $izin->update([
            'status' => $aksi,
            'alasan_penolakan' => $aksi === 'ditolak' ? $alasanPenolakan : null,
            'status_updated_at' => now(),
            'status_updated_by' => $guru->id,
        ]);

        return $izin;
    }
}
