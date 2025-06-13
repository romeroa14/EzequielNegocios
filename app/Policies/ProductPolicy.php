<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->role === 'producer';
    }

    public function view(User $user, Product $product): bool
    {
        return $user->role === 'producer';
    }

    public function create(User $user): bool
    {
        return $user->role === 'producer';
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role === 'producer';
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->role !== 'producer') {
            return false;
        }

        // No permitir eliminar si hay listings activos
        return !$product->listings()->where('status', 'active')->exists();
    }
} 