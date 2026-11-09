<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'nomor_whatsapp'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke data anak (siswa) milik wali murid.
     *
     * @return HasMany<Siswa>
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'user_id');
    }

    /**
     * Relasi ke riwayat pengajuan izin yang diajukan oleh wali murid.
     *
     * @return HasMany<Izin>
     */
    public function izins(): HasMany
    {
        return $this->hasMany(Izin::class, 'user_id');
    }
}
