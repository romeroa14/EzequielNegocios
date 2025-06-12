<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class HomeController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirigir según el rol del usuario
        if ($user->person && $user->person->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        return view('home');
    }

    /**
     * Show the seller dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sellerDashboard()
    {
        $user = Auth::user();
        $listings = $user->person->listings()
            ->withCount(['orders' => function($query) {
                $query->where('status', 'completed');
            }])
            ->get();
            
        $totalSales = $user->person->orders()
            ->where('status', 'completed')
            ->sum('total');
            
        return view('seller.dashboard', compact('listings', 'totalSales'));
    }
}
