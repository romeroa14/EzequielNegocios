<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\ProductListing;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;

class ProductListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }

    public function view(User $user, ProductListing $listing)
    {
        if ($user->role === 'admin') {
            return true;
        }
        return $user->role === 'seller' && $user->id === $listing->person->user_id;
    }

    public function create(User $user)
    {
        return $user->role === 'seller' || $user->role === 'admin';
    }

    public function update(User $user, ProductListing $listing)
    {
        if ($user->role === 'admin') {
            return true;
        }
        return $user->role === 'seller' && $user->id === $listing->person->user_id;
    }

    public function delete(User $user, ProductListing $listing)
    {
        if ($user->role === 'admin') {
            return true;
        }
        return $user->role === 'seller' && $user->id === $listing->person->user_id;
    }
} 