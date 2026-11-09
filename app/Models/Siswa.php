<?php

namespace App\Models;

use Database\Factories\SiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kelas', 'user_id'])]
class Siswa extends Model
{
    protected $table = 'siswa';

    /** @use HasFactory<SiswaFactory> */
    use HasFactory;

    /**
     * Wali murid yang memiliki siswa ini.
     */
    public function waliMurid(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Riwayat izin siswa.
     */
    public function izin(): HasMany
    {
        return $this->hasMany(Izin::class);
    }
}
