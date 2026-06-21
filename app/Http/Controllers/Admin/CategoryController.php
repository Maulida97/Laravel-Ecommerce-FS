<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request): View
    {
        $query = Category::with(['parent'])->withCount('products');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        // Active status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Order categories by hierarchical structure:
        // Root categories group their child categories directly below them.
        // We order by COALESCE(parent_id, id) to keep root + children grouped,
        // then parent_id IS NOT NULL to make root come first, then sort_order.
        $categories = $query->orderByRaw('COALESCE(parent_id, id) ASC')
                            ->orderByRaw('parent_id IS NOT NULL ASC')
                            ->orderBy('sort_order', 'ASC')
                            ->paginate(15)
                            ->withQueryString();

        // Load root categories for the create/edit modal form
        $parentCategories = Category::whereNull('parent_id')->orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(): View
    {
        // Only load parent categories (root categories where parent_id is null)
        $parentCategories = Category::whereNull('parent_id')->orderBy('sort_order')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        // Auto-generate slug if not provided
        $slug = empty($validated['slug']) ? Str::slug($validated['name']) : Str::slug($validated['slug']);
        
        // Ensure slug is unique (in case auto-generated slug conflicts)
        $slugCount = Category::where('slug', $slug)->count();
        if ($slugCount > 0) {
            $slug = $slug . '-' . time();
        }

        $category = new Category();
        $category->name = $validated['name'];
        $category->slug = $slug;
        $category->description = $validated['description'];
        $category->parent_id = $validated['parent_id'];
        $category->is_active = $request->has('is_active');
        $category->sort_order = $validated['sort_order'];

        // Handle local image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->image = Storage::disk('public')->url($path);
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category): View
    {
        // Only load potential parent categories (exclude itself and only root categories where parent_id is null)
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'description' => 'nullable|string|max:1000',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        // Auto-generate slug if not provided
        $slug = empty($validated['slug']) ? Str::slug($validated['name']) : Str::slug($validated['slug']);
        
        // Ensure slug is unique
        $slugCount = Category::where('slug', $slug)->where('id', '!=', $category->id)->count();
        if ($slugCount > 0) {
            $slug = $slug . '-' . time();
        }

        $category->name = $validated['name'];
        $category->slug = $slug;
        $category->description = $validated['description'];
        
        // Prevent assigning self or a subcategory as parent
        if ($validated['parent_id'] != $category->id) {
            $category->parent_id = $validated['parent_id'];
        }
        
        $category->is_active = $request->has('is_active');
        $category->sort_order = $validated['sort_order'];

        // Handle image replacement
        if ($request->hasFile('image')) {
            // Delete old image from public disk if exists
            if ($category->image) {
                $filename = basename($category->image);
                if (Storage::disk('public')->exists('categories/' . $filename)) {
                    Storage::disk('public')->delete('categories/' . $filename);
                }
            }

            $path = $request->file('image')->store('categories', 'public');
            $category->image = Storage::disk('public')->url($path);
        }

        $category->save();

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        // 1. Prevent delete if it has products
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category that contains products.');
        }

        // 2. Prevent delete if it has subcategories (children)
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Cannot delete category that has subcategories.');
        }

        // 3. Delete image from public disk
        if ($category->image) {
            $filename = basename($category->image);
            if (Storage::disk('public')->exists('categories/' . $filename)) {
                Storage::disk('public')->delete('categories/' . $filename);
            }
        }

        // 4. Delete the category
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
