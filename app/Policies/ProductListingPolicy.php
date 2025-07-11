<?php

namespace App\Policies;

use App\Models\ProductListing;
use App\Models\User;
use App\Models\Person;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductListingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Los administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        // Para otros usuarios, verificar rol
        $person = Person::where('email', $user->email)->first();
        return $user->role === 'producer' || ($person && $person->role === 'seller');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductListing $productListing): bool
    {
        // Los administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        $person = Person::where('email', $user->email)->first();
        return $user->role === 'producer' || 
               ($person && $person->role === 'seller' && $productListing->person_id === $person->id) ||
               ($person && $person->role === 'buyer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Los administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        $person = Person::where('email', $user->email)->first();
        return $user->role === 'producer' || ($person && $person->role === 'seller');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductListing $productListing): bool
    {
        // Los administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        $person = Person::where('email', $user->email)->first();
        return ($user->role === 'producer' && $person) || 
               ($person && $person->role === 'seller' && $productListing->person_id === $person->id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductListing $productListing): bool
    {
        // Los administradores tienen acceso total
        if ($user->role === 'admin') {
            return true;
        }

        return $this->update($user, $productListing);
    }
} 