<?php

namespace App\Console\Commands;

use App\Services\DeteksiKehadiranService;
use Illuminate\Console\Command;

class DeteksiBelumFingerprintCommand extends Command
{
    protected $signature = 'kehadiran:deteksi-belum-fingerprint';

    protected $description = 'Deteksi siswa yang belum fingerprint setelah batas waktu.';

    public function handle(DeteksiKehadiranService $service): int
    {
        $siswa = $service->deteksiBelumFingerprint();

        $this->info($siswa->count().' siswa belum tercatat hadir.');

        return self::SUCCESS;
    }
}
