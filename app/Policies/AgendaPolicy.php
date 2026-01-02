<?php

namespace App\Policies;

use App\Models\Agenda;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgendaPolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->roles()->whereIn('name', ['admin','lecturer'])->exists();
    }

    public function update(User $user, Agenda $agenda): bool
    {
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        if ($user->roles()->where('name', 'lecturer')->exists()) {
            $lecturer = $user->lecturer;
            if ($lecturer && $agenda->lecturer_id === $lecturer->id) {
                return true;
            }
        }

        return false;
    }

    public function delete(User $user, Agenda $agenda): bool
    {
        return $this->update($user, $agenda);
    }
}
