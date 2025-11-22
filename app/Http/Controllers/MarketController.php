<?php

namespace App\Http\Controllers;

use App\Models\MarketPrice;
use App\Models\Product;
use App\Models\ExchangeRate;
use App\Models\BcvRate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MarketController extends Controller
{
    /**
     * Mostrar la vista de precios de mercado
     */
    public function index(Request $request)
    {
        // Obtener fecha de filtro (por defecto hoy)
        $selectedDate = $request->get('date', now()->format('Y-m-d'));
        
        // Obtener solo el precio más reciente y activo de cada producto
        $marketPrices = MarketPrice::with(['product', 'updatedBy'])
            ->active()
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('product_id')
            ->map(function ($prices) {
                return $prices->sortByDesc('updated_at')->first();
            })
            ->values()
            ->sortByDesc(function ($price) {
                return $price->updated_at ?? $price->price_date;
            })
            ->values();

        // Obtener la tasa más reciente del scraping para USD
        $usdRate = ExchangeRate::getLatestRate('USD');
        
        // Calcular precios en USD para productos en VES y viceversa
        $marketPrices->each(function ($price) use ($usdRate) {
            if ($price->currency === 'VES' && $usdRate) {
                $price->price_usd = round($price->price / $usdRate->rate, 2);
                $price->price_ves_equivalent = $price->price; // Ya está en VES
                $price->exchange_rate = $usdRate->rate;
                $price->rate_fetched = $usdRate->fetched_at;
            } elseif ($price->currency === 'USD' && $usdRate) {
                $price->price_usd = $price->price; // Ya está en USD
                $price->price_ves_equivalent = round($price->price * $usdRate->rate, 2);
                $price->exchange_rate = $usdRate->rate;
                $price->rate_fetched = $usdRate->fetched_at;
            }
        });

        // Estadísticas con conversiones bidireccionales
        $stats = [
            'total_products' => $marketPrices->count(),
            'total_value_ves' => $marketPrices->sum('price_ves_equivalent'),
            'total_value_usd' => $marketPrices->sum('price_usd'),
            'last_update' => $marketPrices->max('updated_at'),
            'usd_rate' => $usdRate ? $usdRate->rate : null,
            'usd_rate_fetched' => $usdRate ? $usdRate->fetched_at : null,
        ];

        return view('market.index', compact('marketPrices', 'stats'));
    }

    /**
     * Mostrar historial de precios de un producto específico
     */
    public function productHistory($productId)
    {
        $product = \App\Models\Product::with('productCategory')->findOrFail($productId);
        $currentPrice = MarketPrice::where('product_id', $productId)->active()->first();
        $history = \App\Models\MarketPriceHistory::where('product_id', $productId)
            ->with('changedBy')
            ->orderBy('change_date', 'desc')
            ->get();

        return view('market.product-history', compact('product', 'currentPrice', 'history'));
    }

    /**
     * Mostrar precios de una semana específica
     */
    public function weekly(Request $request)
    {
        // Obtener semana seleccionada (por defecto esta semana)
        $weekStart = $request->get('week', now()->startOfWeek()->format('Y-m-d'));
        $startDate = Carbon::parse($weekStart);
        $endDate = $startDate->copy()->endOfWeek();

        // Obtener precios de la semana
        $marketPrices = MarketPrice::with(['product', 'updatedBy'])
            ->active()
            ->whereBetween('price_date', [$startDate, $endDate])
            ->orderBy('price_date', 'desc')
            ->orderBy('product_id')
            ->get()
            ->groupBy('price_date');

        // Obtener semanas disponibles para el filtro
        $availableWeeks = MarketPrice::active()
            ->select('price_date')
            ->distinct()
            ->where('price_date', '>=', now()->subWeeks(8))
            ->orderBy('price_date', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->price_date->startOfWeek()->format('Y-m-d');
            })
            ->map(function ($dates, $weekStart) {
                $start = Carbon::parse($weekStart);
                $end = $start->copy()->endOfWeek();
                return [
                    'value' => $weekStart,
                    'label' => $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y'),
                ];
            })
            ->values();

        return view('market.weekly', compact('marketPrices', 'availableWeeks', 'startDate', 'endDate'));
    }


}
