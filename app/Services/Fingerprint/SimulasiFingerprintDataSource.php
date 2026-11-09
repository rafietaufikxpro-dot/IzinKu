<?php

namespace App\Services\Fingerprint;

use App\Contracts\FingerprintDataSource;
use App\Models\Fingerprint;
use Illuminate\Support\Collection;

class SimulasiFingerprintDataSource implements FingerprintDataSource
{
    /**
     * Implementasi tiruan (stub).
     *
     * Data fingerprint saat ini dibaca dari tabel `fingerprint` yang diisi
     * lewat seeder. Ganti dengan implementasi gateway/API setelah sumber
     * data fingerprint ditentukan.
     */
    public function siswaSudahFingerprint(string $tanggal): Collection
    {
        return Fingerprint::query()
            ->where('tanggal', $tanggal)
            ->pluck('siswa_id');
    }
}
