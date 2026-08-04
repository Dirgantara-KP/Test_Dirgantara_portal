<?php

namespace App\Policies;

use App\Models\TipeSoal;
use App\Models\User;

class TipeSoalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TipeSoal $tipeSoal): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, TipeSoal $tipeSoal): bool
    {
        return true;
    }

    public function delete(User $user, TipeSoal $tipeSoal): bool
    {
        return true;
    }
}

