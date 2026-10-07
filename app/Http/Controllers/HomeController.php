<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Faq;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $settings = SiteSetting::allCached();

        $categories = ProductCategory::roots()
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)])
            ->get();

        $featuredProducts = Product::active()
            ->with('category')
            ->take(6)
            ->get();

        $testimonials = Testimonial::active()
            ->with('product')
            ->take(6)
            ->get();

        $homeFaqs = Faq::onHome()
            ->take(6)
            ->get();

        $branches = Branch::active()
            ->with('schedules')
            ->take(4)
            ->get();

        return view('pages.home', compact(
            'settings',
            'categories',
            'featuredProducts',
            'testimonials',
            'homeFaqs',
            'branches'
        ));
    }
}
