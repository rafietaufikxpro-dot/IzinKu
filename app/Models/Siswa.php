<?php

namespace App\Models;

use Database\Factories\SiswaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Siswa extends Model
{
    /** @use HasFactory<SiswaFactory> */
    use HasFactory;

    /**
     * Nama tabel di database.
     *
     * @var string
     */
    protected $table = 'siswa';

    /**
     * Atribut yang dapat diisi massal.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama',
        'kelas',
        'jenis_kelamin',
    ];

    /**
     * Relasi ke wali murid (User).
     *
     * @return BelongsTo<User, Siswa>
     */
    public function wali(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke riwayat perizinan siswa.
     *
     * @return HasMany<Izin>
     */
    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class, 'siswa_id');
    }
}
