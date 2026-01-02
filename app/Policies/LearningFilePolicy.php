<?php

namespace App\Policies;

use App\Models\LearningFile;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LearningFilePolicy
{
    use HandlesAuthorization;

    public function create(User $user): bool
    {
        return $user->roles()->whereIn('name', ['admin','lecturer'])->exists();
    }

    public function update(User $user, LearningFile $file): bool
    {
        if ($user->roles()->where('name', 'admin')->exists()) {
            return true;
        }

        return $user->id === $file->uploaded_by;
    }

    public function delete(User $user, LearningFile $file): bool
    {
        return $this->update($user, $file);
    }
}
