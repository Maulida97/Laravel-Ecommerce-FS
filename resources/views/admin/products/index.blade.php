@extends('layouts.admin')

@section('title', 'Products — Admin Dashboard')

@section('menu-products-active', 'active')

@section('breadcrumb')
    <span>Products</span> / <span>List</span>
@endsection

@section('styles')
    @vite(['resources/css/products.css'])
@endsection

@section('content')
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert-toast" id="successAlert" style="position: fixed; top: 20px; right: 20px; background: var(--success); color: white; padding: 12px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-toast" id="errorAlert" style="position: fixed; top: 20px; right: 20px; background: var(--danger); color: white; padding: 12px 24px; border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 1000; display: flex; align-items: center; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="page-header">
        <h1 class="page-title">Product List</h1>
        <a href="{{ route('admin.products.create') }}" class="btn-add" id="btnAddProduct" style="text-decoration: none;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('admin.products.index') }}" method="GET" style="display: flex; flex: 1; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
            <div class="search-wrapper">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" id="filterSearch" placeholder="Search by name, SKU..." value="{{ request('search') }}">
            </div>
            
            <select name="category" class="filter-select" id="filterCategory">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            
            <select name="status" class="filter-select" id="filterStatus">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            
            <button type="submit" class="btn-filter" id="btnApplyFilter">Apply Filters</button>
            
            @if(request()->anyFilled(['search', 'category', 'status']))
                <a href="{{ route('admin.products.index') }}" class="btn-clear" id="btnClearFilter">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions Bar (Hidden by default) -->
    <div class="bulk-bar" id="bulkActionsBar" style="display: none;">
        <div class="bulk-info" id="bulkInfoText">0 items selected</div>
        <div class="bulk-actions">
            <button type="button" class="btn-bulk-status" onclick="submitBulkAction('activate')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Activate
            </button>
            <button type="button" class="btn-bulk-status" onclick="submitBulkAction('deactivate')">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                Deactivate
            </button>
            <button type="button" class="btn-bulk-delete" onclick="openBulkDeleteModal()">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Delete Selected
            </button>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <form id="bulkForm" action="{{ route('admin.products.bulk') }}" method="POST">
            @csrf
            <input type="hidden" name="action" id="bulkActionInput" value="">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 40px; padding-left: 24px;">
                            <input type="checkbox" id="selectAllCheckbox" class="row-checkbox">
                        </th>
                        <th style="width: 60px;">Image</th>
                        <th>Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th style="width: 80px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        <tr>
                            <td style="padding-left: 24px;">
                                <input type="checkbox" name="ids[]" value="{{ $product->id }}" class="row-checkbox product-checkbox" onclick="handleRowCheckboxChange()">
                            </td>
                            <td>
                                <div class="product-image-container">
                                    <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}">
                                </div>
                            </td>
                            <td>
                                <div class="product-name-col">
                                    <span class="prod-name">{{ $product->name }}</span>
                                    @if($product->short_description)
                                        <span class="prod-desc" title="{{ $product->short_description }}">{{ $product->short_description }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="prod-sku">{{ $product->sku }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 500;">{{ $product->category->name }}</span>
                            </td>
                            <td>
                                <div class="price-text">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                                @if($product->compare_at_price)
                                    <div class="compare-price-text">Rp {{ number_format($product->compare_at_price, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td>
                                @if($product->stock_quantity > 10)
                                    <span class="stock-badge in-stock">In Stock ({{ $product->stock_quantity }})</span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="stock-badge low-stock">Low Stock ({{ $product->stock_quantity }})</span>
                                @else
                                    <span class="stock-badge out-of-stock">Out of Stock</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_featured)
                                    <span class="featured-badge">Featured</span>
                                @else
                                    <span style="color: var(--text-muted); font-size: var(--text-xs);">No</span>
                                @endif
                            </td>
                            <td>
                                @if($product->is_active)
                                    <span class="status-badge status-active">Active</span>
                                @else
                                    <span class="status-badge status-inactive">Inactive</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div class="actions" style="justify-content: flex-end;">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="action-btn edit" aria-label="Edit {{ $product->name }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <button type="button" class="action-btn delete" onclick="confirmDelete({{ $product->id }}, '{{ addslashes($product->name) }}')" aria-label="Delete {{ $product->name }}">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align: center;">
                                <div class="empty-state" style="padding: 64px 24px; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 12px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="48" height="48" style="color: var(--text-muted);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                    <h3 style="font-size: var(--text-lg); font-weight: 600;">No products found</h3>
                                    <p style="color: var(--text-muted); font-size: var(--text-sm);">Try adjusting filters or add a new product.</p>
                                    <a href="{{ route('admin.products.create') }}" class="btn-add" style="text-decoration: none; margin-top: 8px;">Add Product</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </form>
        
        <!-- Pagination -->
        @if($products->count() > 0)
            <div class="pagination" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-top: 1px solid var(--border-light); background: var(--bg-primary); flex-wrap: wrap; gap: 12px;">
                <div class="pagination-info" style="font-size: var(--text-sm); color: var(--text-secondary);">
                    Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products
                </div>
                <div class="pagination-btns" style="display: flex; align-items: center; gap: 4px;">
                    @if($products->onFirstPage())
                        <span class="page-btn prev-next disabled" style="opacity: 0.5; pointer-events: none; border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}" class="page-btn prev-next" style="border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                    @endif

                    @foreach ($products->getUrlRange(max(1, $products->currentPage() - 1), min($products->lastPage(), $products->currentPage() + 1)) as $page => $url)
                        @if ($page == $products->currentPage())
                            <span class="page-btn active" style="background: var(--primary-600); border-color: var(--primary-600); color: white; height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); font-weight: 500; padding: 0 8px;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn" style="border: 1px solid var(--border); background: var(--bg-primary); color: var(--text-secondary); height: 36px; min-width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md); padding: 0 8px; text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}" class="page-btn prev-next" style="border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    @else
                        <span class="page-btn prev-next disabled" style="opacity: 0.5; pointer-events: none; border: 1px solid var(--border); background: var(--bg-primary); height: 36px; width: 36px; display: inline-flex; align-items: center; justify-content: center; border-radius: var(--radius-md);"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Custom Delete Confirmation Modal Overlay -->
    <div class="modal-overlay" id="deleteOverlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(2px); z-index: 900; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="modal modal-delete" id="deleteModal" style="background: white; border-radius: var(--radius-xl); border: 1px solid var(--border); box-shadow: var(--shadow-xl); width: 100%; max-width: 480px; padding: 24px; position: relative; transform: translateY(-20px); transition: transform 0.3s ease;">
            <form id="deleteForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="modal-body" style="text-align: center;">
                    <div class="delete-icon-wrap" style="width: 56px; height: 56px; background: var(--danger-bg); color: var(--danger); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="delete-title" style="font-size: var(--text-xl); font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Delete Product?</h3>
                    <p class="delete-text" id="deleteText" style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.5; margin-bottom: 24px;">This action cannot be undone. Associated variations and images will be permanently removed.</p>
                </div>
                <div class="delete-footer" style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-light); padding-top: 16px;">
                    <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                    <button type="submit" class="btn-save" style="background: var(--danger);">Delete Permanently</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Confirmation Modal Overlay -->
    <div class="modal-overlay" id="bulkOverlay" style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(2px); z-index: 900; display: none; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
        <div class="modal" id="bulkModal" style="background: white; border-radius: var(--radius-xl); border: 1px solid var(--border); box-shadow: var(--shadow-xl); width: 100%; max-width: 480px; padding: 24px; position: relative; transform: translateY(-20px); transition: transform 0.3s ease;">
            <div class="modal-body" style="text-align: center;">
                <div id="bulkIconWrap" style="width: 56px; height: 56px; border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                    <!-- SVG dynamically placed -->
                </div>
                <h3 id="bulkTitle" style="font-size: var(--text-xl); font-weight: 700; color: var(--text-primary); margin-bottom: 8px;">Confirm Bulk Action</h3>
                <p id="bulkText" style="color: var(--text-secondary); font-size: var(--text-sm); line-height: 1.5; margin-bottom: 24px;">Are you sure you want to perform this action on selected products?</p>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-light); padding-top: 16px;">
                <button type="button" class="btn-cancel" onclick="closeBulkModal()">Cancel</button>
                <button type="button" class="btn-save" id="btnConfirmBulk" style="background: var(--primary-600);">Confirm</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';

        // Auto fadeout for alerts
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => successAlert.remove(), 500);
            }, 4000);
        }

        if (errorAlert) {
            setTimeout(() => {
                errorAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                errorAlert.style.opacity = '0';
                errorAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => errorAlert.remove(), 500);
            }, 4000);
        }

        // --- Bulk Selection Logic ---
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const bulkInfoText = document.getElementById('bulkInfoText');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                handleRowCheckboxChange();
            });
        }

        window.handleRowCheckboxChange = function() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            
            if (checkedCount > 0) {
                bulkActionsBar.style.display = 'flex';
                bulkInfoText.textContent = `${checkedCount} product(s) selected`;
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = (checkedCount === productCheckboxes.length);
                }
            } else {
                bulkActionsBar.style.display = 'none';
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
            }
        };

        // --- Bulk Action Submit handler ---
        const bulkForm = document.getElementById('bulkForm');
        const bulkActionInput = document.getElementById('bulkActionInput');
        const bulkOverlay = document.getElementById('bulkOverlay');
        const bulkModal = document.getElementById('bulkModal');
        const btnConfirmBulk = document.getElementById('btnConfirmBulk');
        const bulkIconWrap = document.getElementById('bulkIconWrap');
        const bulkTitle = document.getElementById('bulkTitle');
        const bulkText = document.getElementById('bulkText');

        window.submitBulkAction = function(action) {
            bulkActionInput.value = action;
            
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            
            if (action === 'delete') {
                bulkIconWrap.style.background = 'var(--danger-bg)';
                bulkIconWrap.style.color = 'var(--danger)';
                bulkIconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
                bulkTitle.textContent = 'Bulk Delete Products?';
                bulkText.innerHTML = `Are you sure you want to permanently delete the <strong>${checkedCount}</strong> selected products? All variations and images will be removed.`;
                btnConfirmBulk.className = 'btn-save';
                btnConfirmBulk.style.background = 'var(--danger)';
                btnConfirmBulk.textContent = 'Delete Selected';
            } else {
                bulkIconWrap.style.background = 'var(--primary-50)';
                bulkIconWrap.style.color = 'var(--primary-600)';
                bulkIconWrap.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="28" height="28"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                bulkTitle.textContent = action === 'activate' ? 'Activate Products?' : 'Deactivate Products?';
                bulkText.innerHTML = `Are you sure you want to change the status of the <strong>${checkedCount}</strong> selected products?`;
                btnConfirmBulk.className = 'btn-save';
                btnConfirmBulk.style.background = 'var(--primary-600)';
                btnConfirmBulk.textContent = 'Confirm Changes';
            }

            bulkOverlay.style.display = 'flex';
            setTimeout(() => {
                bulkOverlay.style.opacity = '1';
                bulkModal.style.transform = 'translateY(0)';
            }, 50);
            document.body.style.overflow = 'hidden';
        };

        window.closeBulkModal = function() {
            bulkOverlay.style.opacity = '0';
            bulkModal.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                bulkOverlay.style.display = 'none';
            }, 300);
            document.body.style.overflow = '';
        };

        if (btnConfirmBulk && bulkForm) {
            btnConfirmBulk.addEventListener('click', function() {
                bulkForm.submit();
            });
        }

        // --- Delete Single Product Logic ---
        const deleteOverlay = document.getElementById('deleteOverlay');
        const deleteModal = document.getElementById('deleteModal');
        const deleteForm = document.getElementById('deleteForm');
        const deleteText = document.getElementById('deleteText');

        window.confirmDelete = function(id, name) {
            deleteForm.setAttribute('action', `/admin/products/${id}`);
            deleteText.innerHTML = `Are you sure you want to delete product <strong>"${name}"</strong>? This action cannot be undone. All variations and images will be permanently removed.`;
            deleteOverlay.style.display = 'flex';
            setTimeout(() => {
                deleteOverlay.style.opacity = '1';
                deleteModal.style.transform = 'translateY(0)';
            }, 50);
            document.body.style.overflow = 'hidden';
        };

        window.closeDeleteModal = function() {
            deleteOverlay.style.opacity = '0';
            deleteModal.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                deleteOverlay.style.display = 'none';
            }, 300);
            document.body.style.overflow = '';
        };

        // Click outside overlay to close modal
        if (deleteOverlay) {
            deleteOverlay.addEventListener('click', function(e) {
                if (e.target === deleteOverlay) closeDeleteModal();
            });
        }
        if (bulkOverlay) {
            bulkOverlay.addEventListener('click', function(e) {
                if (e.target === bulkOverlay) closeBulkModal();
            });
        }

        // Escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeDeleteModal();
                closeBulkModal();
            }
        });

    })();
</script>
@endsection
