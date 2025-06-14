<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
        $seller = Auth::guard('web')->user()->person;
        
        // Estadísticas generales
        $generalStats = [
            'total_sales' => $seller->sellerOrders()->count(),
            'total_revenue' => $seller->sellerOrders()
                ->where('status', 'delivered')
                ->sum('total_amount'),
            'active_listings' => $seller->productListings()
                ->where('status', 'active')
                ->count(),
            'total_views' => $seller->productListings()
                ->sum('views'),
        ];

        // Ventas por mes (últimos 12 meses)
        $monthlySales = $seller->sellerOrders()
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Productos más vendidos
        $topProducts = $seller->productListings()
            ->with('product')
            ->select('product_listings.*')
            ->addSelect(DB::raw('COUNT(order_items.id) as total_sold'))
            ->leftJoin('order_items', 'product_listings.id', '=', 'order_items.product_listing_id')
            ->groupBy('product_listings.id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Estadísticas de interacción
        $interactionStats = [
            'total_messages' => $seller->conversations()
                ->withCount('messages')
                ->get()
                ->sum('messages_count'),
            'response_rate' => $this->calculateResponseRate($seller),
            'avg_response_time' => $this->calculateAverageResponseTime($seller),
        ];

        return view('seller.statistics.index', compact(
            'generalStats',
            'monthlySales',
            'topProducts',
            'interactionStats'
        ));
    }

    private function calculateResponseRate($seller)
    {
        $conversations = $seller->conversations()->get();
        if ($conversations->isEmpty()) {
            return 0;
        }

        $responded = $conversations->filter(function ($conversation) use ($seller) {
            return $conversation->messages()
                ->where('sender_id', $seller->id)
                ->exists();
        })->count();

        return ($responded / $conversations->count()) * 100;
    }

    private function calculateAverageResponseTime($seller)
    {
        $responseTimes = [];
        
        foreach ($seller->conversations as $conversation) {
            $messages = $conversation->messages()
                ->orderBy('created_at')
                ->get()
                ->groupBy('sender_id');

            if (isset($messages[$conversation->buyer_id])) {
                foreach ($messages[$conversation->buyer_id] as $buyerMessage) {
                    $response = $conversation->messages()
                        ->where('sender_id', $seller->id)
                        ->where('created_at', '>', $buyerMessage->created_at)
                        ->orderBy('created_at')
                        ->first();

                    if ($response) {
                        $responseTimes[] = $response->created_at->diffInMinutes($buyerMessage->created_at);
                    }
                }
            }
        }

        return empty($responseTimes) ? 0 : round(array_sum($responseTimes) / count($responseTimes));
    }
} 