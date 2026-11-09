<?php

namespace App\Contracts;

use Illuminate\Support\Collection;

interface FingerprintDataSource
{
    /**
     * Ambil daftar ID siswa yang sudah tercatat fingerprint pada tanggal tertentu.
     *
     * @return Collection<int, int|string>
     */
    public function siswaSudahFingerprint(string $tanggal): Collection;
}
