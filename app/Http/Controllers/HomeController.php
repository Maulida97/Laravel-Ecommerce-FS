<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the dynamic storefront landing page.
     */
    public function index(): View
    {
        $slides = collect();
        $featuredCategories = collect();
        $featuredProducts = collect();

        // Safe database checks for tests / fresh setups
        if (\Illuminate\Support\Facades\Schema::hasTable('slides')) {
            $slides = Slide::where('is_active', true)
                ->orderBy('sort_order')
                ->get();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('categories')) {
            $featuredCategories = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->withCount(['products' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('sort_order')
                ->get();
        }

        if (\Illuminate\Support\Facades\Schema::hasTable('products')) {
            $featuredProducts = Product::active()
                ->featured()
                ->with(['category', 'primaryImage', 'images'])
                ->limit(6)
                ->get();
        }

        return view('landing-page', compact('slides', 'featuredCategories', 'featuredProducts'));
    }
}
