<?php

namespace App\Services;

use App\Contracts\FingerprintDataSource;
use App\Models\Siswa;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class DeteksiKehadiranService
{
    public function __construct(
        private readonly FingerprintDataSource $fingerprint,
    ) {}

    /**
     * Deteksi siswa yang belum fingerprint pada tanggal tertentu.
     *
     * Aturan bisnis: siswa yang belum fingerprint dianggap belum hadir,
     * kecuali punya izin berstatus disetujui yang mencakup tanggal tersebut.
     *
     * @return Collection<int, Siswa>
     */
    public function deteksiBelumFingerprint(?CarbonInterface $tanggal = null): Collection
    {
        $tanggal ??= today();
        $tanggalString = $tanggal->toDateString();

        $siswaSudahFingerprint = $this->fingerprint->siswaSudahFingerprint($tanggalString);

        return Siswa::query()
            ->whereNotIn('id', $siswaSudahFingerprint)
            ->whereDoesntHave('izins', function (Builder $query) use ($tanggalString) {
                $query->where('status', 'disetujui')
                    ->whereDate('tanggal_mulai', '<=', $tanggalString)
                    ->whereDate('tanggal_selesai', '>=', $tanggalString);
            })
            ->orderBy('nama')
            ->get();
    }
}
