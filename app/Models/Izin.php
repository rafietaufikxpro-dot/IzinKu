<?php

namespace App\Models;

use Database\Factories\IzinFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Izin extends Model
{
    /** @use HasFactory<IzinFactory> */
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'izin';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'siswa_id',
        'jenis_izin',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'alasan',
        'surat_sakit',
        'status_konfirmasi',
        'status',
        'catatan_penolakan',
        'diverifikasi_oleh',
        'diverifikasi_pada',
    ];

    /**
     * Tipe casting atribut.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'jumlah_hari' => 'integer',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    /**
     * Relasi ke wali murid pemohon.
     *
     * @return BelongsTo<User, Izin>
     */
    public function wali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke siswa yang bersangkutan.
     *
     * @return BelongsTo<Siswa, Izin>
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    /**
     * Relasi ke guru piket yang memverifikasi.
     *
     * @return BelongsTo<User, Izin>
     */
    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    /**
     * Cek apakah jenis perizinan adalah sakit.
     */
    public function isSakit(): bool
    {
        return $this->jenis_izin === 'sakit';
    }

    /**
     * Cek apakah status ACC masih menunggu.
     */
    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }
}
