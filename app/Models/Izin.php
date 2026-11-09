<?php

namespace App\Models;

use Database\Factories\IzinFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'siswa_id',
    'user_id',
    'alasan',
    'tanggal_mulai',
    'tanggal_selesai',
    'surat_sakit_path',
    'status',
    'alasan_penolakan',
    'catatan',
    'confirmed_at',
    'confirmed_by',
    'status_updated_at',
    'status_updated_by',
])]
class Izin extends Model
{
    protected $table = 'izin';

    /** @use HasFactory<IzinFactory> */
    use HasFactory;

    /**
     * Status yang diperbolehkan.
     *
     * @var array<int, string>
     */
    public const STATUSES = ['menunggu', 'disetujui', 'ditolak'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'confirmed_at' => 'datetime',
            'status_updated_at' => 'datetime',
        ];
    }

    /**
     * Siswa yang mengajukan izin.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Wali murid yang mengajukan izin.
     */
    public function waliMurid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Pengguna yang terakhir mengubah status.
     */
    public function pengubahStatus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }
}
