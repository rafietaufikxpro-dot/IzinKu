<?php

namespace App\Services;

use App\Models\Izin;
use App\Models\Siswa;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class IzinService
{
    /**
     * Hitung total hari perizinan (inklusif).
     */
    public function calculateDuration(string|Carbon $startDate, string|Carbon $endDate): int
    {
        $start = is_string($startDate) ? Carbon::parse($startDate)->startOfDay() : $startDate->copy()->startOfDay();
        $end = is_string($endDate) ? Carbon::parse($endDate)->startOfDay() : $endDate->copy()->startOfDay();

        if ($end->lessThan($start)) {
            throw new InvalidArgumentException('Tanggal selesai tidak boleh sebelum tanggal mulai.');
        }

        return (int) $start->diffInDays($end) + 1;
    }

    /**
     * Ambil batas maksimal durasi izin dari konfigurasi.
     */
    public function getMaxDurationDays(): int
    {
        return (int) config('izin.max_duration_days', 3);
    }

    /**
     * Cek apakah durasi hari melewati batas konfigurasi sekolah.
     */
    public function isDurationExceeded(int $durationDays): bool
    {
        $max = $this->getMaxDurationDays();

        return $max > 0 && $durationDays > $max;
    }

    /**
     * Ambil daftar siswa yang terhubung dengan akun wali murid.
     *
     * @return Collection<int, Siswa>
     */
    public function getSiswaForWali(User $user): Collection
    {
        return $user->siswas()->orderBy('nama')->get();
    }

    /**
     * Simpan pengajuan izin baru ke database.
     *
     * @param  array{
     *     siswa_id: int|string,
     *     jenis_izin: string,
     *     tanggal_mulai: string,
     *     tanggal_selesai: string,
     *     alasan: string
     * }  $data
     */
    public function createIzin(User $user, array $data, ?UploadedFile $suratSakit = null): Izin
    {
        $duration = $this->calculateDuration($data['tanggal_mulai'], $data['tanggal_selesai']);

        if ($this->isDurationExceeded($duration)) {
            throw new InvalidArgumentException(
                "Durasi izin ({$duration} hari) melebihi batas maksimal ({$this->getMaxDurationDays()} hari) sesuai aturan sekolah."
            );
        }

        if ($data['jenis_izin'] === 'sakit' && ! $suratSakit) {
            throw new InvalidArgumentException('Izin sakit wajib melampirkan surat sakit / surat keterangan dokter.');
        }

        return DB::transaction(function () use ($user, $data, $duration, $suratSakit) {
            $pathSurat = null;

            if ($suratSakit !== null) {
                $disk = config('izin.surat_sakit.disk', 'local');
                $folder = config('izin.surat_sakit.folder', 'surat_sakit');
                $pathSurat = $suratSakit->store($folder, $disk);
            }

            return Izin::create([
                'user_id' => $user->id,
                'siswa_id' => (int) $data['siswa_id'],
                'jenis_izin' => $data['jenis_izin'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'jumlah_hari' => $duration,
                'alasan' => $data['alasan'],
                'surat_sakit' => $pathSurat,
                'status_konfirmasi' => 'menunggu',
                'status' => 'menunggu',
            ]);
        });
    }
}
