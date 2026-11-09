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
}
