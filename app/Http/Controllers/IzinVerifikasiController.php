<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccIzinRequest;
use App\Models\Izin;
use App\Services\IzinVerifikasiService;

class IzinVerifikasiController extends Controller
{
    public function __construct(
        private readonly IzinVerifikasiService $verifikasiService,
    ) {}

    /**
     * Daftar izin untuk verifikasi.
     */
    public function index()
    {
        $this->authorize('viewAny', Izin::class);

        $izin = Izin::with('siswa')
            ->latest()
            ->paginate(10);

        return view('verifikasi-izin.index', compact('izin'));
    }

    /**
     * Detail izin untuk verifikasi.
     */
    public function show(Izin $izin)
    {
        $this->authorize('view', $izin);

        return view('verifikasi-izin.show', compact('izin'));
    }

    /**
     * Proses ACC izin.
     */
    public function acc(AccIzinRequest $request, Izin $izin)
    {
        $this->authorize('acc', $izin);

        $izin = $this->verifikasiService->accIzin(
            $request->user(),
            $izin,
            $request->validated('aksi'),
            $request->validated('alasan_penolakan'),
        );

        return redirect()
            ->route('izin-verifikasi.show', $izin)
            ->with('success', $izin->status === 'disetujui' ? 'Izin disetujui.' : 'Izin ditolak.');
    }
}
