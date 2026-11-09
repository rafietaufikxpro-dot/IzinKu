<?php

namespace App\Policies;

use App\Models\Izin;
use App\Models\User;

class IzinPolicy
{
    /**
     * Tentukan apakah pengguna boleh melihat izin.
     */
    public function view(User $user, Izin $izin): bool
    {
        if ($user->hasRole(['guru', 'guru_piket', 'admin'])) {
            return true;
        }

        return $izin->user_id === $user->id;
    }

    /**
     * Tentukan apakah pengguna boleh melihat daftar izin.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['guru', 'guru_piket', 'admin']);
    }

    /**
     * Tentukan apakah pengguna boleh memberi ACC (persetujuan) izin.
     */
    public function acc(User $user, Izin $izin): bool
    {
        return $user->hasRole(config('izin.role_pemberi_acc'));
    }
}
