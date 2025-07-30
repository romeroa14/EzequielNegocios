<?php

namespace App\Http\Controllers;

use App\Models\ProductListing;
use App\Models\Person;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Log;

class WelcomeController extends Controller
{
    public function index()
    {
        try {
            $exchangeRates = [
                'usd' => ProductListing::getUsdRate(),
                'eur' => ProductListing::getEurRate()
            ];

            // Obtener estadísticas para la página
            $stats = [
                'sellers' => Person::where('role', 'seller')->count(),
                'listings' => ProductListing::where('status', 'active')->count(),
                'categories' => ProductCategory::where('is_active', true)->count()
            ];

            Log::info('Exchange rates loaded:', $exchangeRates);

            return view('welcome', compact('exchangeRates', 'stats'));
        } catch (\Exception $e) {
            Log::error('Error loading welcome page:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Si hay un error, al menos pasamos las estadísticas
            $stats = [
                'sellers' => Person::where('role', 'seller')->count(),
                'listings' => ProductListing::where('status', 'active')->count(),
                'categories' => ProductCategory::where('is_active', true)->count()
            ];

            return view('welcome', [
                'exchangeRates' => [
                    'usd' => ['rate' => null, 'fetched_at' => null],
                    'eur' => ['rate' => null, 'fetched_at' => null]
                ],
                'stats' => $stats
            ]);
        }
    }
} 