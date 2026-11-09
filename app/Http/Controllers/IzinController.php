<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIzinRequest;
use App\Models\Izin;
use App\Services\IzinService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class IzinController extends Controller
{
    /**
     * Tampilkan form pengajuan izin siswa (Task 3 / FR-04).
     */
    public function create(Request $request, IzinService $izinService): View
    {
        $user = $request->user();
        $siswas = $izinService->getSiswaForWali($user);
        $maxDays = $izinService->getMaxDurationDays();

        return view('FormIzinSiswa.index', [
            'siswas' => $siswas,
            'maxDays' => $maxDays,
        ]);
    }

    /**
     * Proses penyimpanan data pengajuan izin siswa (Task 3 & 5 / FR-04 & FR-06).
     */
    public function store(StoreIzinRequest $request, IzinService $izinService): RedirectResponse
    {
        $izin = $izinService->createIzin(
            user: $request->user(),
            data: $request->validated(),
            suratSakit: $request->file('surat_sakit')
        );

        return redirect()->route('izin.create')->with(
            'success',
            "Pengajuan izin untuk {$izin->siswa->nama} berhasil disimpan! Status: Menunggu Konfirmasi."
        );
    }

    /**
     * Daftar izin yang diajukan wali murid (Task 4).
     */
    public function index()
    {
        $izin = Izin::with('siswa')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('izin.index', compact('izin'));
    }

    /**
     * Detail pengajuan izin (Task 4).
     */
    public function show(Izin $izin)
    {
        $this->authorize('view', $izin);

        return view('izin.show', compact('izin'));
    }

    /**
     * Unduh berkas surat sakit (Task 4).
     */
    public function downloadSuratSakit(Izin $izin)
    {
        $this->authorize('view', $izin);

        abort_if($izin->surat_sakit === null, 404);

        return Storage::disk(config('izin.surat_sakit.disk', 'local'))->download($izin->surat_sakit);
    }
}
