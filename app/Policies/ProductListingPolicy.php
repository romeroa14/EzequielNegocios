<?php

namespace App\Policies;

use App\Models\Person;
use App\Models\ProductListing;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductListingPolicy
{
    use HandlesAuthorization;

    public function viewAny(Person $person)
    {
        return $person->isSeller();
    }

    public function view(Person $person, ProductListing $listing)
    {
        return $person->id === $listing->person_id;
    }

    public function create(Person $person)
    {
        return $person->isSeller();
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