<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $settings = SiteSetting::allCached();

        $search = trim((string) ($request->query('search') ?? $request->query('q') ?? ''));
        $categorySlug = $request->query('kategori');

        $currentCategory = null;
        $categoryIds = [];

        if ($categorySlug) {
            $currentCategory = ProductCategory::where('slug', $categorySlug)->with('children')->first();

            if ($currentCategory) {
                if ($currentCategory->children->isNotEmpty()) {
                    $categoryIds = $currentCategory->children->pluck('id')->push($currentCategory->id)->all();
                } else {
                    $categoryIds = [$currentCategory->id];
                }
            }
        }

        $products = Product::active()
            ->with(['category.parent'])
            ->when(! empty($categoryIds), fn ($query) => $query->whereIn('category_id', $categoryIds))
            ->when(! empty($search), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('function', 'like', "%{$search}%")
                        ->orWhere('indication', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->paginate(12)
            ->withQueryString();

        $categoryTree = ProductCategory::roots()
            ->with([
                'children' => fn ($q) => $q->withCount(['products' => fn ($pq) => $pq->where('is_active', true)]),
            ])
            ->withCount(['products' => fn ($pq) => $pq->where('is_active', true)])
            ->get();

        return view('pages.catalog.index', compact(
            'products',
            'categoryTree',
            'currentCategory',
            'search',
            'settings'
        ));
    }

    public function show(string $slug): View
    {
        $settings = SiteSetting::allCached();

        $product = Product::active()
            ->where('slug', $slug)
            ->with([
                'category.parent',
                'images',
                'testimonials' => fn ($q) => $q->active()->take(3),
            ])
            ->firstOrFail();

        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(3)
            ->get();

        return view('pages.catalog.show', compact('product', 'relatedProducts', 'settings'));
    }
}
