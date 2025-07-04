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
        return $user->role === 'seller' || $user->role === 'admin';
    }

    public function view(User $user, Product $product): bool
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }

    public function create(User $user): bool
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }

    public function update(User $user, Product $product): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return $user->role === 'seller' && $user->id === $product->person->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role !== 'seller' || $user->id !== $product->person->user_id) {
            return false;
        }

        // No permitir eliminar si hay listings activos
        return !$product->listings()->where('status', 'active')->exists();
    }
} 