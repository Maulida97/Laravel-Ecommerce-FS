<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\VariantAttributeValue;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Exception;

class ProductController extends Controller
{
    protected CloudinaryService $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'primaryImage']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Status filter
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $products = $query->orderBy('created_at', 'DESC')
                          ->paginate(15)
                          ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products,sku',
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Auto-slug if not provided
            $slug = empty($validated['slug']) ? Str::slug($validated['name']) : Str::slug($validated['slug']);
            $slugCount = Product::where('slug', $slug)->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . time();
            }

            $product = new Product();
            $product->name = $validated['name'];
            $product->slug = $slug;
            $product->category_id = $validated['category_id'];
            $product->description = $validated['description'] ?? null;
            $product->short_description = $validated['short_description'] ?? null;
            $product->price = $validated['price'];
            $product->compare_at_price = $validated['compare_at_price'] ?? null;
            $product->sku = $validated['sku'];
            $product->stock_quantity = $request->has('has_variants') ? 0 : ($validated['stock_quantity'] ?? 0);
            $product->weight = $validated['weight'];
            $product->is_active = $request->has('is_active');
            $product->is_featured = $request->has('is_featured');
            $product->meta_title = $validated['meta_title'] ?? null;
            $product->meta_description = $validated['meta_description'] ?? null;
            $product->save();

            // Handle Images
            $primaryIndex = $request->input('primary_image_index', 0);
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $cloudinaryResult = $this->cloudinaryService->upload($imageFile, 'products');
                    $imageUrl = $cloudinaryResult['secure_url'];
                    $publicId = $cloudinaryResult['public_id'];

                    // Fallback to local storage if Cloudinary is in dummy/fallback mode
                    if (str_contains($publicId, 'dummy_cloudinary_id')) {
                        $path = $imageFile->store('products', 'public');
                        $imageUrl = Storage::disk('public')->url($path);
                        $publicId = 'local_products_' . basename($path);
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imageUrl,
                        'public_id' => $publicId,
                        'alt_text' => $product->name,
                        'sort_order' => $index,
                        'is_primary' => $index == $primaryIndex,
                    ]);
                }
            }

            // Handle Variants
            if ($request->has('has_variants') && $request->filled('variants_json')) {
                $variantsData = json_decode($request->input('variants_json'), true);
                $totalStock = 0;

                if (is_array($variantsData)) {
                    foreach ($variantsData as $variantItem) {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'variant_name' => $variantItem['name'],
                            'sku' => $variantItem['sku'],
                            'price_adjustment' => $variantItem['price_adjustment'] ?? 0.00,
                            'stock_quantity' => $variantItem['stock_quantity'] ?? 0,
                            'is_active' => filter_var($variantItem['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                        ]);

                        $totalStock += $variant->stock_quantity;

                        $valueIds = [];
                        if (isset($variantItem['attributes']) && is_array($variantItem['attributes'])) {
                            foreach ($variantItem['attributes'] as $attr) {
                                $attributeName = trim($attr['name']);
                                $attributeValue = trim($attr['value']);
                                $colorCode = isset($attr['color_code']) ? trim($attr['color_code']) : null;

                                $attribute = VariantAttribute::firstOrCreate(['name' => $attributeName]);
                                $value = VariantAttributeValue::firstOrCreate(
                                    [
                                        'variant_attribute_id' => $attribute->id,
                                        'value' => $attributeValue,
                                    ],
                                    [
                                        'color_code' => $colorCode
                                    ]
                                );

                                $valueIds[] = $value->id;
                            }
                        }

                        if (!empty($valueIds)) {
                            $variant->combinations()->attach($valueIds);
                        }
                    }
                    // Sync total stock to product
                    $product->stock_quantity = $totalStock;
                    $product->save();
                }
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error creating product: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        // Load product with relations
        $product->load(['images', 'variants.combinations.attribute']);
        
        // Build JSON representation of variants for Javascript combination editor
        $variantsJsonData = [];
        foreach ($product->variants as $variant) {
            $attrs = [];
            foreach ($variant->combinations as $comb) {
                $attrs[] = [
                    'name' => $comb->attribute->name,
                    'value' => $comb->value,
                    'color_code' => $comb->color_code,
                ];
            }
            $variantsJsonData[] = [
                'name' => $variant->variant_name,
                'sku' => $variant->sku,
                'price_adjustment' => (float)$variant->price_adjustment,
                'stock_quantity' => (int)$variant->stock_quantity,
                'is_active' => (bool)$variant->is_active,
                'attributes' => $attrs,
            ];
        }
        
        $variantsJson = json_encode($variantsJsonData);

        return view('admin.products.edit', compact('product', 'categories', 'variantsJson'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'sku' => 'required|string|max:100|unique:products,sku,' . $product->id,
            'stock_quantity' => 'nullable|integer|min:0',
            'weight' => 'required|integer|min:0',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Auto-slug if not provided
            $slug = empty($validated['slug']) ? Str::slug($validated['name']) : Str::slug($validated['slug']);
            $slugCount = Product::where('slug', $slug)->where('id', '!=', $product->id)->count();
            if ($slugCount > 0) {
                $slug = $slug . '-' . time();
            }

            $product->name = $validated['name'];
            $product->slug = $slug;
            $product->category_id = $validated['category_id'];
            $product->description = $validated['description'] ?? null;
            $product->short_description = $validated['short_description'] ?? null;
            $product->price = $validated['price'];
            $product->compare_at_price = $validated['compare_at_price'] ?? null;
            $product->sku = $validated['sku'];
            $product->weight = $validated['weight'];
            $product->is_active = $request->has('is_active');
            $product->is_featured = $request->has('is_featured');
            $product->meta_title = $validated['meta_title'] ?? null;
            $product->meta_description = $validated['meta_description'] ?? null;
            
            if (!$request->has('has_variants')) {
                $product->stock_quantity = $validated['stock_quantity'] ?? 0;
            }
            $product->save();

            // Handle deleted images
            if ($request->filled('deleted_images')) {
                $deletedImageIds = explode(',', $request->input('deleted_images'));
                foreach ($deletedImageIds as $imgId) {
                    $imgId = trim($imgId);
                    if (empty($imgId)) continue;
                    
                    $img = ProductImage::find($imgId);
                    if ($img && $img->product_id == $product->id) {
                        // Delete file
                        if (str_contains($img->public_id, 'local_products_')) {
                            $filename = str_replace('local_products_', '', $img->public_id);
                            if (Storage::disk('public')->exists('products/' . $filename)) {
                                Storage::disk('public')->delete('products/' . $filename);
                            }
                        } else {
                            $this->cloudinaryService->delete($img->public_id);
                        }
                        $img->delete();
                    }
                }
            }

            // Handle new uploaded images
            $existingImagesCount = $product->images()->count();
            $primaryIndex = $request->input('primary_image_index', 0);
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    $cloudinaryResult = $this->cloudinaryService->upload($imageFile, 'products');
                    $imageUrl = $cloudinaryResult['secure_url'];
                    $publicId = $cloudinaryResult['public_id'];

                    // Fallback to local storage if Cloudinary is in dummy/fallback mode
                    if (str_contains($publicId, 'dummy_cloudinary_id')) {
                        $path = $imageFile->store('products', 'public');
                        $imageUrl = Storage::disk('public')->url($path);
                        $publicId = 'local_products_' . basename($path);
                    }

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image_url' => $imageUrl,
                        'public_id' => $publicId,
                        'alt_text' => $product->name,
                        'sort_order' => $existingImagesCount + $index,
                        'is_primary' => false, // Will adjust primary image next
                    ]);
                }
            }

            // Sync/Adjust primary image flag
            $allImages = $product->fresh()->images;
            if ($allImages->isNotEmpty()) {
                // Determine which image ID is marked primary
                // The form sends primary_image_id if it was an existing image, OR primary_image_index if new
                $primaryImageId = $request->input('primary_image_id');
                
                if ($primaryImageId) {
                    foreach ($allImages as $img) {
                        $img->is_primary = $img->id == $primaryImageId;
                        $img->save();
                    }
                } else {
                    // Fallback to index matching (e.g. if primary_image_index is provided)
                    $targetIndex = (int)$request->input('primary_image_index', 0);
                    if ($targetIndex >= $allImages->count()) {
                        $targetIndex = 0;
                    }
                    foreach ($allImages as $idx => $img) {
                        $img->is_primary = $idx === $targetIndex;
                        $img->save();
                    }
                }
            }

            // Handle Variants update
            if ($request->has('has_variants')) {
                // Delete old variants
                // onDelete('cascade') handles variant combinations automatically
                $product->variants()->delete();

                if ($request->filled('variants_json')) {
                    $variantsData = json_decode($request->input('variants_json'), true);
                    $totalStock = 0;

                    if (is_array($variantsData)) {
                        foreach ($variantsData as $variantItem) {
                            $variant = ProductVariant::create([
                                'product_id' => $product->id,
                                'variant_name' => $variantItem['name'],
                                'sku' => $variantItem['sku'],
                                'price_adjustment' => $variantItem['price_adjustment'] ?? 0.00,
                                'stock_quantity' => $variantItem['stock_quantity'] ?? 0,
                                'is_active' => filter_var($variantItem['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN),
                            ]);

                            $totalStock += $variant->stock_quantity;

                            $valueIds = [];
                            if (isset($variantItem['attributes']) && is_array($variantItem['attributes'])) {
                                foreach ($variantItem['attributes'] as $attr) {
                                    $attributeName = trim($attr['name']);
                                    $attributeValue = trim($attr['value']);
                                    $colorCode = isset($attr['color_code']) ? trim($attr['color_code']) : null;

                                    $attribute = VariantAttribute::firstOrCreate(['name' => $attributeName]);
                                    $value = VariantAttributeValue::firstOrCreate(
                                        [
                                            'variant_attribute_id' => $attribute->id,
                                            'value' => $attributeValue,
                                        ],
                                        [
                                            'color_code' => $colorCode
                                        ]
                                    );

                                    $valueIds[] = $value->id;
                                }
                            }

                            if (!empty($valueIds)) {
                                $variant->combinations()->attach($valueIds);
                            }
                        }
                        // Update stock count
                        $product->stock_quantity = $totalStock;
                        $product->save();
                    }
                }
            } else {
                // If variants are turned off, delete all variants
                $product->variants()->delete();
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Error updating product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Delete associated images from storage (Cloudinary or local)
            foreach ($product->images as $image) {
                if (str_contains($image->public_id, 'local_products_')) {
                    $filename = str_replace('local_products_', '', $image->public_id);
                    if (Storage::disk('public')->exists('products/' . $filename)) {
                        Storage::disk('public')->delete('products/' . $filename);
                    }
                } else {
                    $this->cloudinaryService->delete($image->public_id);
                }
            }

            // Eloquent constraints/onDelete('cascade') will delete images and variants automatically
            $product->delete();

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')->with('error', 'Error deleting product: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions (delete, status updates).
     */
    public function bulk(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
            'action' => 'required|string|in:delete,activate,deactivate',
        ]);

        $ids = $request->input('ids');
        $action = $request->input('action');

        DB::beginTransaction();
        try {
            if ($action === 'delete') {
                foreach ($ids as $id) {
                    $product = Product::find($id);
                    if ($product) {
                        // Delete storage files
                        foreach ($product->images as $image) {
                            if (str_contains($image->public_id, 'local_products_')) {
                                $filename = str_replace('local_products_', '', $image->public_id);
                                if (Storage::disk('public')->exists('products/' . $filename)) {
                                    Storage::disk('public')->delete('products/' . $filename);
                                }
                            } else {
                                $this->cloudinaryService->delete($image->public_id);
                            }
                        }
                        $product->delete();
                    }
                }
                $message = 'Selected products deleted successfully.';
            } elseif ($action === 'activate') {
                Product::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Selected products activated successfully.';
            } elseif ($action === 'deactivate') {
                Product::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Selected products deactivated successfully.';
            }

            DB::commit();
            return redirect()->route('admin.products.index')->with('success', $message);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.products.index')->with('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }
}
