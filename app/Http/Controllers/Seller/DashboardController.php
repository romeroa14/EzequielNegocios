<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductListing;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $activeListings = ProductListing::where('person_id', auth()->user()->person->id)
            ->where('status', 'active')
            ->count();

        $pendingListings = ProductListing::where('person_id', auth()->user()->person->id)
            ->where('status', 'pending')
            ->count();

        $soldOutListings = ProductListing::where('person_id', auth()->user()->person->id)
            ->where('status', 'sold_out')
            ->count();

        $recentListings = ProductListing::where('person_id', auth()->user()->person->id)
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'activeListings',
            'pendingListings',
            'soldOutListings',
            'recentListings'
        ));
    }
} 