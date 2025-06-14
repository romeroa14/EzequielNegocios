<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\ProductListing;
use App\Models\Order;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $seller = Auth::guard('web')->user()->person;
        
        // Estadísticas generales
        $stats = [
            'total_listings' => $seller->productListings()->count(),
            'active_listings' => $seller->productListings()->where('status', 'active')->count(),
            'total_sales' => $seller->sellerOrders()->count(),
            'pending_orders' => $seller->sellerOrders()->where('status', 'pending')->count(),
            'total_contacts' => $seller->conversations()->count(),
            'new_messages' => $seller->conversations()
                ->whereHas('messages', function($query) {
                    $query->where('read', false)
                        ->where('sender_id', '!=', Auth::id());
                })->count(),
        ];

        // Últimos listados
        $latestListings = $seller->productListings()
            ->with('product')
            ->latest()
            ->take(5)
            ->get();

        // Últimas órdenes
        $latestOrders = $seller->sellerOrders()
            ->with(['buyer', 'items'])
            ->latest()
            ->take(5)
            ->get();

        // Últimos contactos
        $latestContacts = $seller->conversations()
            ->with(['messages' => function($query) {
                $query->latest()->take(1);
            }, 'buyer'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', compact(
            'stats',
            'latestListings',
            'latestOrders',
            'latestContacts'
        ));
    }
} 