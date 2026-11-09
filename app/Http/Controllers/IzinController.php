<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIzinRequest;
use App\Models\Izin;
use App\Models\Siswa;
use App\Services\IzinService;
use Illuminate\Support\Facades\Storage;

class IzinController extends Controller
{
    public function __construct(
        private readonly IzinService $izinService,
    ) {}

    /**
     * Daftar izin yang diajukan wali murid.
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
     * Form pengajuan izin.
     */
    public function create()
    {
        $siswa = Siswa::where('user_id', auth()->id())
            ->orderBy('nama')
            ->get();

        return view('izin.create', compact('siswa'));
    }

    /**
     * Simpan pengajuan izin baru.
     */
    public function store(StoreIzinRequest $request)
    {
        $izin = $this->izinService->buatIzin(
            $request->user(),
            $request->validated(),
            $request->file('surat_sakit'),
        );

        return redirect()
            ->route('izin.show', $izin)
            ->with('success', 'Pengajuan izin berhasil dibuat dan menunggu konfirmasi.');
    }

    /**
     * Detail pengajuan izin.
     */
    public function show(Izin $izin)
    {
        $this->authorize('view', $izin);

        return view('izin.show', compact('izin'));
    }

    /**
     * Unduh berkas surat sakit.
     */
    public function downloadSuratSakit(Izin $izin)
    {
        $this->authorize('view', $izin);

        abort_if($izin->surat_sakit_path === null, 404);

        return Storage::disk('surat_sakit')->download($izin->surat_sakit_path);
    }
}
