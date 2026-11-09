<?php

namespace App\Http\Controllers;

use App\Services\DeteksiKehadiranService;

class KehadiranController extends Controller
{
    public function __construct(
        private readonly DeteksiKehadiranService $kehadiranService,
    ) {}

    /**
     * Daftar siswa yang belum fingerprint.
     */
    public function belumFingerprint()
    {
        $tanggal = today();
        $siswa = $this->kehadiranService->deteksiBelumFingerprint($tanggal);

        return view('kehadiran.belum-fingerprint', [
            'siswa' => $siswa,
            'tanggal' => $tanggal,
        ]);
    }
}
