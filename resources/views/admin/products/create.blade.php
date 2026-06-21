@extends('layouts.admin')

@section('title', 'Create Product — Tokoku.id')

@section('menu-products-active', 'active')

@section('breadcrumb')
    <span>Products</span> / <span>Create</span>
@endsection

@section('styles')
    @vite(['resources/css/products.css'])
@endsection

@section('content')
<div class="product-container" style="max-width: 1200px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">Create Product</h1>
        <a href="{{ route('admin.products.index') }}" class="btn-cancel" style="text-decoration: none; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    @if(session('error'))
        <div class="alert-toast" style="background: var(--danger); color: white; padding: 12px 24px; border-radius: var(--radius-md); margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
        @csrf
        <input type="hidden" name="variants_json" id="variantsJsonInput" value="">
        <input type="hidden" name="primary_image_index" id="primaryImageIndexInput" value="0">

        <div class="form-grid">
            <!-- Left Column: Details, Images, Variants -->
            <div style="display: flex; flex-direction: column; gap: var(--space-6); min-width: 0;">
                <!-- Basic Info Card -->
                <div class="form-card">
                    <h3 style="font-size: var(--text-base); font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">Basic Information</h3>
                    
                    <div class="form-group">
                        <label class="form-label required" for="prodName">Product Name</label>
                        <input type="text" name="name" class="form-input @error('name') error @enderror" id="prodName" value="{{ old('name') }}" placeholder="e.g. Cotton T-Shirt" required>
                        @error('name')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label required" for="prodSlug">Slug</label>
                        <input type="text" name="slug" class="form-input @error('slug') error @enderror" id="prodSlug" value="{{ old('slug') }}" placeholder="e.g. cotton-t-shirt">
                        @error('slug')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="prodCategory">Category</label>
                            <select name="category_id" class="form-select @error('category_id') error @enderror" id="prodCategory" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label required" for="prodSku">SKU</label>
                            <input type="text" name="sku" class="form-input @error('sku') error @enderror" id="prodSku" value="{{ old('sku') }}" placeholder="e.g. TSHIRT-COT-01" required>
                            @error('sku')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prodShortDesc">Short Description</label>
                        <input type="text" name="short_description" class="form-input @error('short_description') error @enderror" id="prodShortDesc" value="{{ old('short_description') }}" placeholder="Brief summary of the product...">
                        @error('short_description')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="prodDesc">Full Description</label>
                        <textarea name="description" class="form-textarea @error('description') error @enderror" id="prodDesc" placeholder="Describe your product details here...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-msg">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="prodPrice">Price (Rp)</label>
                            <input type="number" name="price" class="form-input @error('price') error @enderror" id="prodPrice" value="{{ old('price') }}" placeholder="e.g. 150000" min="0" required>
                            @error('price')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="prodComparePrice">Compare Price (Rp)</label>
                            <input type="number" name="compare_at_price" class="form-input @error('compare_at_price') error @enderror" id="prodComparePrice" value="{{ old('compare_at_price') }}" placeholder="e.g. 200000" min="0">
                            @error('compare_at_price')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label required" for="prodWeight">Weight (grams)</label>
                            <input type="number" name="weight" class="form-input @error('weight') error @enderror" id="prodWeight" value="{{ old('weight', 200) }}" placeholder="e.g. 200" min="0" required>
                            @error('weight')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group" id="stockQuantityGroup">
                            <label class="form-label" for="prodStock">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-input @error('stock_quantity') error @enderror" id="prodStock" value="{{ old('stock_quantity', 0) }}" placeholder="e.g. 50" min="0">
                            @error('stock_quantity')
                                <div class="error-msg">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Product Images Card -->
                <div class="form-card">
                    <h3 style="font-size: var(--text-base); font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">Product Images</h3>
                    <div class="image-uploader-container">
                        <div class="upload-zone" id="uploadZone">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" id="uploadZoneIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="upload-text">Click or drag image here</span>
                            <span class="upload-subtext">PNG, JPG, WEBP up to 2MB (Select multiple files)</span>
                            <input type="file" name="images[]" class="upload-input" id="prodImages" accept="image/*" multiple>
                        </div>
                        <div class="uploaded-images-grid" id="uploadedImagesGrid">
                            <!-- Thumbnails dynamically rendered -->
                        </div>
                    </div>
                </div>

                <!-- Product Variants Card -->
                <div class="form-card">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <h3 style="font-size: var(--text-base); font-weight: 600; color: var(--text-primary);">Product Variations</h3>
                        <div class="toggle-wrap">
                            <input type="checkbox" name="has_variants" id="hasVariantsInput" value="1" {{ old('has_variants') ? 'checked' : '' }} style="display: none;">
                            <div class="toggle {{ old('has_variants') ? 'active' : '' }}" id="hasVariantsToggle" role="switch" aria-checked="{{ old('has_variants') ? 'true' : 'false' }}" tabindex="0"><div class="toggle-knob"></div></div>
                            <span class="toggle-label" style="font-weight: 600;">This product has variations (e.g. Size, Color)</span>
                        </div>
                    </div>

                    <div class="variants-wrapper" id="variantsWrapper" style="{{ old('has_variants') ? 'display: block;' : 'display: none;' }}">
                        <div class="variants-container">
                            <div id="attributesList">
                                <!-- Attributes list dynamically rendered -->
                            </div>

                            <button type="button" class="btn-add-attribute" id="btnAddAttribute">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Option Attribute
                            </button>

                            <!-- Combinations Table Grid -->
                            <div class="combinations-section" id="combinationsSection" style="display: none;">
                                <h4 class="combinations-title">Variation List & Pricing</h4>
                                <div class="combinations-table-wrap">
                                    <table class="combinations-table">
                                        <thead>
                                            <tr>
                                                <th>Variant</th>
                                                <th>SKU</th>
                                                <th>Price Adjustment (Rp)</th>
                                                <th>Stock</th>
                                                <th style="width: 80px; text-align: center;">Active</th>
                                            </tr>
                                        </thead>
                                        <tbody id="combinationsTableBody">
                                            <!-- Rows dynamically rendered -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Save & SEO -->
            <div style="display: flex; flex-direction: column; gap: var(--space-6); position: sticky; top: calc(var(--header-height) + 24px);">
                <!-- Settings & Submit Card -->
                <div class="form-card">
                    <h3 style="font-size: var(--text-base); font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">Publish Settings</h3>
                    
                    <div class="form-group" style="margin-bottom: 20px;">
                        <div class="toggle-wrap">
                            <input type="checkbox" name="is_active" id="isActiveInput" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="display: none;">
                            <div class="toggle {{ old('is_active', '1') == '1' ? 'active' : '' }}" id="isActiveToggle" role="switch" aria-checked="true" tabindex="0"><div class="toggle-knob"></div></div>
                            <span class="toggle-label" id="isActiveLabel">Active (Visible in catalog)</span>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 24px;">
                        <div class="toggle-wrap">
                            <input type="checkbox" name="is_featured" id="isFeaturedInput" value="1" {{ old('is_featured') ? 'checked' : '' }} style="display: none;">
                            <div class="toggle {{ old('is_featured') ? 'active' : '' }}" id="isFeaturedToggle" role="switch" aria-checked="false" tabindex="0"><div class="toggle-knob"></div></div>
                            <span class="toggle-label" id="isFeaturedLabel">Featured product</span>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <button type="submit" class="btn-save" style="width: 100%; height: 44px;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Create Product
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn-cancel" style="width: 100%; height: 44px; text-decoration: none;">Cancel</a>
                    </div>
                </div>

                <!-- SEO Settings Card -->
                <div class="form-card">
                    <h3 style="font-size: var(--text-base); font-weight: 600; margin-bottom: 20px; color: var(--text-primary);">SEO Search Settings</h3>
                    
                    <div class="form-group">
                        <label class="form-label" for="metaTitle">SEO Title</label>
                        <input type="text" name="meta_title" class="form-input" id="metaTitle" value="{{ old('meta_title') }}" placeholder="Search engine title...">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="metaDesc">SEO Description</label>
                        <textarea name="meta_description" class="form-textarea" id="metaDesc" style="min-height: 80px;" placeholder="Search engine description...">{{ old('meta_description') }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';

        // --- Slug Auto-generate logic ---
        const prodName = document.getElementById('prodName');
        const prodSlug = document.getElementById('prodSlug');
        let isSlugEdited = false;

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        if (prodName && prodSlug) {
            prodName.addEventListener('input', function() {
                if (!isSlugEdited) {
                    prodSlug.value = slugify(prodName.value);
                    syncVariantSKUs();
                }
            });

            prodSlug.addEventListener('input', function() {
                isSlugEdited = prodSlug.value.length > 0;
            });
        }

        // --- Status and Featured toggles ---
        setupToggle('isActiveToggle', 'isActiveInput', 'isActiveLabel', 'Active (Visible in catalog)', 'Inactive (Hidden)');
        setupToggle('isFeaturedToggle', 'isFeaturedInput', 'isFeaturedLabel', 'Featured product', 'Not featured');

        function setupToggle(toggleId, inputId, labelId, activeText, inactiveText) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            const label = document.getElementById(labelId);

            if (toggle && input) {
                toggle.addEventListener('click', () => {
                    const active = toggle.classList.toggle('active');
                    input.checked = active;
                    toggle.setAttribute('aria-checked', active);
                    if (label) label.textContent = active ? activeText : inactiveText;
                });
                toggle.addEventListener('keydown', (e) => {
                    if (e.key === ' ' || e.key === 'Enter') {
                        e.preventDefault();
                        toggle.click();
                    }
                });
            }
        }

        // --- Multiple Images Upload & Preview logic ---
        const prodImages = document.getElementById('prodImages');
        const uploadZone = document.getElementById('uploadZone');
        const uploadedImagesGrid = document.getElementById('uploadedImagesGrid');
        const primaryImageIndexInput = document.getElementById('primaryImageIndexInput');

        let imageFilesList = []; // Array of files

        if (prodImages) {
            prodImages.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (file.size > 2 * 1024 * 1024) {
                        alert(`File "${file.name}" is too large (max 2MB).`);
                        return;
                    }
                    imageFilesList.push(file);
                });
                renderImagesGrid();
                updateInputFiles();
            });
        }

        if (uploadZone) {
            uploadZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadZone.classList.add('dragover');
            });
            uploadZone.addEventListener('dragleave', () => {
                uploadZone.classList.remove('dragover');
            });
            uploadZone.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadZone.classList.remove('dragover');
                const files = Array.from(e.dataTransfer.files);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert(`File "${file.name}" is too large (max 2MB).`);
                            return;
                        }
                        imageFilesList.push(file);
                    }
                });
                renderImagesGrid();
                updateInputFiles();
            });
        }

        function updateInputFiles() {
            // Update the underlying file input files list
            const dataTransfer = new DataTransfer();
            imageFilesList.forEach(file => dataTransfer.items.add(file));
            prodImages.files = dataTransfer.files;
        }

        function renderImagesGrid() {
            uploadedImagesGrid.innerHTML = '';
            
            // Adjust primary index if it goes out of bounds
            let primaryIndex = parseInt(primaryImageIndexInput.value);
            if (primaryIndex >= imageFilesList.length) {
                primaryIndex = 0;
                primaryImageIndexInput.value = 0;
            }

            imageFilesList.forEach((file, index) => {
                const card = document.createElement('div');
                card.className = 'image-preview-card';

                const img = document.createElement('img');
                const reader = new FileReader();
                reader.onload = (e) => img.src = e.target.result;
                reader.readAsDataURL(file);
                card.appendChild(img);

                // Delete button
                const removeBtn = document.createElement('div');
                removeBtn.className = 'img-remove-btn';
                removeBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
                removeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    imageFilesList.splice(index, 1);
                    if (primaryIndex === index) {
                        primaryImageIndexInput.value = 0;
                    } else if (primaryIndex > index) {
                        primaryImageIndexInput.value = primaryIndex - 1;
                    }
                    renderImagesGrid();
                    updateInputFiles();
                });
                card.appendChild(removeBtn);

                // Primary Badge selector
                const primaryBadge = document.createElement('div');
                const isPrimary = (index === primaryIndex);
                primaryBadge.className = `primary-badge ${isPrimary ? 'is-primary' : 'not-primary'}`;
                primaryBadge.textContent = isPrimary ? 'Primary Image' : 'Set as Primary';
                primaryBadge.addEventListener('click', () => {
                    primaryImageIndexInput.value = index;
                    renderImagesGrid();
                });
                card.appendChild(primaryBadge);

                uploadedImagesGrid.appendChild(card);
            });
        }

        // --- Variants and Attributes logic ---
        const hasVariantsToggle = document.getElementById('hasVariantsToggle');
        const hasVariantsInput = document.getElementById('hasVariantsInput');
        const variantsWrapper = document.getElementById('variantsWrapper');
        const stockQuantityGroup = document.getElementById('stockQuantityGroup');
        const prodStock = document.getElementById('prodStock');

        const btnAddAttribute = document.getElementById('btnAddAttribute');
        const attributesList = document.getElementById('attributesList');
        const combinationsSection = document.getElementById('combinationsSection');
        const combinationsTableBody = document.getElementById('combinationsTableBody');
        const variantsJsonInput = document.getElementById('variantsJsonInput');

        let attributesState = []; // [{ id: Date.now(), name: 'Size', values: ['S', 'M'] }]
        let currentCombinations = []; // [{ attributes: {Size: 'S'}, sku: '', price_adjustment: 0, stock_quantity: 0, is_active: true }]

        if (hasVariantsToggle && hasVariantsInput && variantsWrapper) {
            hasVariantsToggle.addEventListener('click', () => {
                const active = hasVariantsToggle.classList.toggle('active');
                hasVariantsInput.checked = active;
                hasVariantsToggle.setAttribute('aria-checked', active);
                variantsWrapper.style.display = active ? 'block' : 'none';
                
                if (active) {
                    prodStock.value = 0;
                    prodStock.disabled = true;
                    stockQuantityGroup.style.opacity = '0.5';
                    if (attributesState.length === 0) {
                        // Add default Size attribute to start
                        addAttributeRow('Size');
                    } else {
                        regenerateCombinations();
                    }
                } else {
                    prodStock.disabled = false;
                    stockQuantityGroup.style.opacity = '1';
                }
            });
            hasVariantsToggle.addEventListener('keydown', (e) => {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    hasVariantsToggle.click();
                }
            });

            // Initialize on page load if old value exists
            if (hasVariantsInput.checked) {
                prodStock.value = 0;
                prodStock.disabled = true;
                stockQuantityGroup.style.opacity = '0.5';
            }
        }

        if (btnAddAttribute) {
            btnAddAttribute.addEventListener('click', () => addAttributeRow());
        }

        function addAttributeRow(name = '') {
            const attrId = Date.now() + Math.random().toString(36).substr(2, 5);
            const attrData = { id: attrId, name: name, values: [] };
            attributesState.push(attrData);

            const row = document.createElement('div');
            row.className = 'attribute-row';
            row.setAttribute('data-id', attrId);

            row.innerHTML = `
                <div class="attribute-row-header">
                    <div style="flex: 1; max-width: 200px;">
                        <select class="form-select attr-name-select" style="height: 36px;" required>
                            <option value="Size" ${name === 'Size' ? 'selected' : ''}>Size</option>
                            <option value="Color" ${name === 'Color' ? 'selected' : ''}>Color</option>
                            <option value="Material" ${name === 'Material' ? 'selected' : ''}>Material</option>
                        </select>
                    </div>
                    <button type="button" class="btn-remove-attribute">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="12" height="12"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Remove
                    </button>
                </div>
                <div class="tags-input-container">
                    <input type="text" class="tags-input" placeholder="Type value and press Enter (e.g. Red, Blue)">
                </div>
            `;

            // Setup select listener
            const select = row.querySelector('.attr-name-select');
            select.addEventListener('change', () => {
                attrData.name = select.value;
                regenerateCombinations();
            });

            // Setup remove button listener
            row.querySelector('.btn-remove-attribute').addEventListener('click', () => {
                attributesState = attributesState.filter(a => a.id !== attrId);
                row.remove();
                regenerateCombinations();
            });

            // Setup tag inputs listener
            const tagInput = row.querySelector('.tags-input');
            const tagsContainer = row.querySelector('.tags-input-container');

            tagInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const val = tagInput.value.trim();
                    if (val && !attrData.values.includes(val)) {
                        attrData.values.push(val);
                        addTagBadge(tagsContainer, tagInput, val, attrData);
                        regenerateCombinations();
                    }
                    tagInput.value = '';
                }
            });

            attributesList.appendChild(row);
            regenerateCombinations();
        }

        function addTagBadge(container, inputElement, val, attrData) {
            const badge = document.createElement('span');
            badge.className = 'tag-badge';
            
            // Support color swatch if Color is chosen
            let swatch = '';
            if (attrData.name === 'Color') {
                const colorHex = getColorHexFallback(val);
                swatch = `<span style="display:inline-block; width:10px; height:10px; border-radius:50%; background:${colorHex}; border:1px solid rgba(0,0,0,0.15)"></span> `;
                badge.innerHTML = `${swatch}${val}`;
            } else {
                badge.textContent = val;
            }

            const removeBtn = document.createElement('span');
            removeBtn.className = 'tag-remove';
            removeBtn.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>';
            removeBtn.addEventListener('click', () => {
                attrData.values = attrData.values.filter(v => v !== val);
                badge.remove();
                regenerateCombinations();
            });

            badge.appendChild(removeBtn);
            container.insertBefore(badge, inputElement);
        }

        function getColorHexFallback(colorName) {
            const colors = {
                'red': '#ef4444',
                'blue': '#3b82f6',
                'green': '#10b981',
                'yellow': '#f59e0b',
                'black': '#0f172a',
                'white': '#ffffff',
                'gray': '#6b7280',
                'pink': '#ec4899',
                'purple': '#a855f7',
                'orange': '#f97316',
                'brown': '#78350f',
            };
            return colors[colorName.toLowerCase()] || '#cbd5e1';
        }

        // Generate Cartesian Product of Attribute values
        function getCartesianProduct(arrays) {
            return arrays.reduce((acc, curr) => {
                return acc.flatMap(d => curr.map(e => [...d, e]));
            }, [[]]);
        }

        function regenerateCombinations() {
            // Keep attributes with values
            const activeAttributes = attributesState.filter(a => a.values.length > 0 && a.name !== '');

            if (activeAttributes.length === 0) {
                combinationsSection.style.display = 'none';
                combinationsTableBody.innerHTML = '';
                serializeVariants();
                return;
            }

            combinationsSection.style.display = 'block';

            // Arrays for cartesian product
            const valueLists = activeAttributes.map(a => a.values);
            const cartesianCombos = getCartesianProduct(valueLists);

            const newCombinations = [];

            cartesianCombos.forEach(combo => {
                // Map to variant combination object
                // combo is an array of values (e.g. ['Red', 'S']) matching the activeAttributes order
                const comboAttributes = {};
                activeAttributes.forEach((attr, idx) => {
                    comboAttributes[attr.name] = combo[idx];
                });

                // Generate Name
                const comboName = combo.join(' - ');

                // Check if combination already existed in previous state
                const existing = currentCombinations.find(c => {
                    return Object.keys(comboAttributes).every(key => c.attributes[key] === comboAttributes[key]);
                });

                const prodSkuBase = prodSku.value.trim() || 'PROD';
                const skuNameStr = combo.join('-').toUpperCase().replace(/\s+/g, '');
                const generatedSku = `${prodSkuBase}-${skuNameStr}`;

                newCombinations.push({
                    name: comboName,
                    sku: existing ? existing.sku : generatedSku,
                    price_adjustment: existing ? existing.price_adjustment : 0,
                    stock_quantity: existing ? existing.stock_quantity : 0,
                    is_active: existing ? existing.is_active : true,
                    attributes: activeAttributes.map((attr, idx) => ({
                        name: attr.name,
                        value: combo[idx],
                        color_code: attr.name === 'Color' ? getColorHexFallback(combo[idx]) : null
                    }))
                });
            });

            currentCombinations = newCombinations;
            renderCombinationsTable();
            serializeVariants();
        }

        function syncVariantSKUs() {
            // Sync SKUs when product base SKU changes
            if (!hasVariantsInput.checked) return;
            
            const prodSkuBase = prodSku.value.trim() || 'PROD';
            
            currentCombinations.forEach(comb => {
                const suffix = comb.name.split(' - ').join('-').toUpperCase().replace(/\s+/g, '');
                comb.sku = `${prodSkuBase}-${suffix}`;
            });
            renderCombinationsTable();
            serializeVariants();
        }

        // Watch base SKU changes to sync variants
        const prodSku = document.getElementById('prodSku');
        if (prodSku) {
            prodSku.addEventListener('input', syncVariantSKUs);
        }

        function renderCombinationsTable() {
            combinationsTableBody.innerHTML = '';

            currentCombinations.forEach((comb, idx) => {
                const tr = document.createElement('tr');
                
                tr.innerHTML = `
                    <td class="comb-name">${comb.name}</td>
                    <td>
                        <input type="text" class="comb-input-text comb-sku-input" style="width: 150px;" value="${comb.sku}" required>
                    </td>
                    <td>
                        <input type="number" class="comb-input-text comb-price-input" style="width: 120px;" value="${comb.price_adjustment}" min="0" required>
                    </td>
                    <td>
                        <input type="number" class="comb-input-text comb-stock-input" style="width: 80px;" value="${comb.stock_quantity}" min="0" required>
                    </td>
                    <td style="text-align: center;">
                        <div class="toggle-wrap" style="justify-content: center; gap: 0;">
                            <div class="toggle toggle-comb ${comb.is_active ? 'active' : ''}" role="switch" tabindex="0"><div class="toggle-knob"></div></div>
                        </div>
                    </td>
                `;

                // Add input listeners
                tr.querySelector('.comb-sku-input').addEventListener('input', (e) => {
                    comb.sku = e.target.value;
                    serializeVariants();
                });
                tr.querySelector('.comb-price-input').addEventListener('input', (e) => {
                    comb.price_adjustment = parseFloat(e.target.value) || 0;
                    serializeVariants();
                });
                tr.querySelector('.comb-stock-input').addEventListener('input', (e) => {
                    comb.stock_quantity = parseInt(e.target.value) || 0;
                    serializeVariants();
                });

                // Toggle active listener
                const combToggle = tr.querySelector('.toggle-comb');
                combToggle.addEventListener('click', () => {
                    const active = combToggle.classList.toggle('active');
                    comb.is_active = active;
                    serializeVariants();
                });

                combinationsTableBody.appendChild(tr);
            });
        }

        function serializeVariants() {
            if (hasVariantsInput.checked) {
                variantsJsonInput.value = JSON.stringify(currentCombinations);
            } else {
                variantsJsonInput.value = '';
            }
        }

        // Intercept form submit to ensure variants json is updated
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', function(e) {
                serializeVariants();
            });
        }

    })();
</script>
@endsection
