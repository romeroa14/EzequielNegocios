<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\ProductListing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // Cualquier usuario autenticado puede ver listings
    }

    public function view(User $user, ProductListing $listing): bool
    {
        return true; // Cualquier usuario autenticado puede ver un listing específico
    }

    public function create(User $user): bool
    {
        return $user->role === 'producer';
    }

    public function update(Person $person, ProductListing $listing)
    {
        return $person->id === $listing->person_id;
    }

    public function delete(Person $person, ProductListing $listing)
    {
        return $person->id === $listing->person_id;
    }
} 