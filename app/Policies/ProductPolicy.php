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
        return $user->person && $user->person->role === 'seller';
    }

    public function view(User $user, Product $product): bool
    {
        return $user->person && $user->person->role === 'seller';
    }

    public function create(User $user): bool
    {
        return $user->person && $user->person->role === 'seller';
    }

    public function update(User $user, Product $product): bool
    {
        return $user->person && $user->person->role === 'seller';
    }

    public function delete(User $user, Product $product): bool
    {
        if (!$user->person || $user->person->role !== 'seller') {
            return false;
        }

        // No permitir eliminar si hay listings activos
        return !$product->listings()->where('status', 'active')->exists();
    }
} 