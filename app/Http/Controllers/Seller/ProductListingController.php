<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductListingController extends Controller
{
    public function index()
    {
        $listings = ProductListing::where('seller_id', auth('admin')->id())
            ->with(['product'])
            ->latest()
            ->paginate(10);

        return view('seller.listings.index', compact('listings'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        return view('seller.listings.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'quality_grade' => 'required|in:premium,standard,economic',
            'harvest_date' => 'required|date',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'location_city' => 'required|string',
            'location_state' => 'required|string',
        ]);

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('listings', 'public');
                $images[] = $path;
            }
        }

        $validated['seller_id'] = auth('admin')->id();
        $validated['images'] = $images;
        $validated['status'] = 'pending';

        $listing = ProductListing::create($validated);

        return redirect()->route('seller.listings.index')
            ->with('success', 'Listing created successfully.');
    }

    public function edit(ProductListing $listing)
    {
        $this->authorize('update', $listing);
        $products = Product::where('is_active', true)->get();
        return view('seller.listings.edit', compact('listing', 'products'));
    }

    public function update(Request $request, ProductListing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'quantity_available' => 'required|integer|min:0',
            'quality_grade' => 'required|in:premium,standard,economic',
            'harvest_date' => 'required|date',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'location_city' => 'required|string',
            'location_state' => 'required|string',
        ]);

        $images = $listing->images ?? [];
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($images as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            
            // Store new images
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('listings', 'public');
                $images[] = $path;
            }
        }

        $listing->update([
            'product_id' => $validated['product_id'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'unit_price' => $validated['unit_price'],
            'quantity_available' => $validated['quantity_available'],
            'quality_grade' => $validated['quality_grade'],
            'harvest_date' => $validated['harvest_date'],
            'images' => $images,
            'location_city' => $validated['location_city'],
            'location_state' => $validated['location_state'],
            'status' => 'pending' // Vuelve a pending al actualizar
        ]);

        return redirect()->route('seller.listings.index')
            ->with('success', 'Producto actualizado exitosamente y en espera de aprobación.');
    }

    public function destroy(ProductListing $listing)
    {
        $this->authorize('delete', $listing);

        // Delete images
        if (!empty($listing->images)) {
            foreach ($listing->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $listing->delete();

        return redirect()->route('seller.listings.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
