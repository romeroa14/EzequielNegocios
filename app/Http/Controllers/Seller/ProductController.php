<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'subcategory'])
            ->where('is_active', true)
            ->latest()
            ->paginate(10);

        return view('seller.products.index', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::where('is_active', true)->get();
        return view('seller.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_type' => 'required|in:kg,ton,saco,caja,unidad',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'seasonal_info' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unit_type' => $validated['unit_type'],
            'image' => $imagePath ?? null,
            'seasonal_info' => $validated['seasonal_info'] ?? [],
            'is_active' => true,
        ]);

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product)
    {
        $categories = ProductCategory::where('is_active', true)->get();
        $subcategories = ProductSubcategory::where('category_id', $product->category_id)
            ->where('is_active', true)
            ->get();

        return view('seller.products.edit', compact('product', 'categories', 'subcategories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'subcategory_id' => 'required|exists:product_subcategories,id',
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'unit_type' => 'required|in:kg,ton,saco,caja,unidad',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'seasonal_info' => 'nullable|array',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'unit_type' => $validated['unit_type'],
            'seasonal_info' => $validated['seasonal_info'] ?? [],
        ]);

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        // Check if product has active listings
        if ($product->listings()->where('status', 'active')->exists()) {
            return back()->with('error', 'No se puede eliminar el producto porque tiene publicaciones activas.');
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('seller.products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function getSubcategories(ProductCategory $category)
    {
        $subcategories = ProductSubcategory::where('category_id', $category->id)
            ->where('is_active', true)
            ->get(['id', 'name']);

        return response()->json($subcategories);
    }
} 