@extends('layouts.admin')

@section('title', 'Categories — Admin Dashboard')

@section('menu-categories-active', 'active')

@section('breadcrumb')
    <span>Categories</span> / <span>List</span>
@endsection

@section('styles')
    @vite(['resources/css/categories.css'])
@endsection

@section('content')
    <!-- Notifications -->
    @if(session('success'))
        <div class="alert-toast" id="successAlert">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error-toast" id="errorAlert">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="page-header">
        <h1 class="page-title">Category List</h1>
        <button type="button" class="btn-add" id="btnAddCategory" onclick="openAddModal()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Category
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('admin.categories.index') }}" method="GET" style="display: flex; flex: 1; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
            <div class="search-wrapper">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" id="filterSearch" placeholder="Search categories..." value="{{ request('search') }}">
            </div>
            
            <select name="status" class="filter-select" id="filterStatus">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            
            <button type="submit" class="btn-filter" id="btnApplyFilter">Apply Filters</button>
            
            @if(request()->anyFilled(['search', 'status']))
                <a href="{{ route('admin.categories.index') }}" class="btn-clear" id="btnClearFilter">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Clear Filters
                </a>
            @endif
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:60px;">Image</th>
                    <th class="sortable" data-sort="name">Name</th>
                    <th class="sortable" data-sort="slug">Slug</th>
                    <th>Parent</th>
                    <th class="sortable" data-sort="products">Products</th>
                    <th class="sortable" data-sort="sort">Sort</th>
                    <th>Status</th>
                    <th style="width:80px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="categoryTableBody">
                @forelse($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="cat-image">
                            @else
                                <div class="cat-image-placeholder">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="cat-name">{{ $category->name }}</div>
                            <div class="cat-desc" title="{{ $category->description }}">{{ $category->description ?: 'No description' }}</div>
                        </td>
                        <td>
                            <span class="cat-slug">{{ $category->slug }}</span>
                        </td>
                        <td>
                            @if($category->parent)
                                <span class="parent-badge sub">{{ $category->parent->name }}</span>
                            @else
                                <span class="parent-badge root">Root</span>
                            @endif
                        </td>
                        <td>
                            <span class="products-count {{ $category->products_count === 0 ? 'zero' : '' }}">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td>
                            <span class="sort-num">{{ $category->sort_order }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td style="text-align:right;">
                            <div class="actions" style="justify-content: flex-end;">
                                <button type="button" 
                                        class="action-btn edit" 
                                        data-id="{{ $category->id }}"
                                        data-name="{{ $category->name }}"
                                        data-slug="{{ $category->slug }}"
                                        data-description="{{ $category->description }}"
                                        data-parent-id="{{ $category->parent_id }}"
                                        data-sort-order="{{ $category->sort_order }}"
                                        data-is-active="{{ $category->is_active ? '1' : '0' }}"
                                        data-image="{{ $category->image }}"
                                        onclick="openEditModal(this)"
                                        aria-label="Edit {{ $category->name }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="action-btn delete" onclick="confirmDelete({{ $category->id }}, '{{ addslashes($category->name) }}')" aria-label="Delete {{ $category->name }}">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <h3>No categories found</h3>
                                <p>Try adjusting your search or filters.</p>
                                <button type="button" class="btn-add" onclick="openAddModal()">Add Category</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination — always shown when there are items -->
        @if($categories->count() > 0)
            <div class="pagination">
                <div class="pagination-info">
                    Showing {{ $categories->firstItem() }}-{{ $categories->lastItem() }} of {{ $categories->total() }} categories
                </div>
                <div class="pagination-btns">
                    @if($categories->onFirstPage())
                        <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                    @else
                        <a href="{{ $categories->previousPageUrl() }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                    @endif

                    @foreach ($categories->getUrlRange(max(1, $categories->currentPage() - 1), min($categories->lastPage(), $categories->currentPage() + 1)) as $page => $url)
                        @if ($page == $categories->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($categories->hasMorePages())
                        <a href="{{ $categories->nextPageUrl() }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    @else
                        <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

<!-- Add/Edit Category Modal Overlay -->
<div class="modal-overlay" id="modalOverlay"></div>
<div class="modal" id="categoryModal">
    <div class="modal-header">
        <h2 class="modal-title" id="modalTitle">Add Category</h2>
        <button type="button" class="modal-close" id="modalClose" aria-label="Close modal"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
    </div>
    <div class="modal-body">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">
            <input type="hidden" name="edit_id" id="editIdInput" value="{{ old('edit_id') }}">
            
            <div class="form-group">
                <label class="form-label required" for="catName">Category Name</label>
                <input type="text" name="name" class="form-input @error('name') error @enderror" id="catName" value="{{ old('name') }}" placeholder="e.g. Electronics" required>
                @error('name')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label required" for="catSlug">Slug</label>
                <input type="text" name="slug" class="form-input @error('slug') error @enderror" id="catSlug" value="{{ old('slug') }}" placeholder="e.g. electronics">
                @error('slug')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="catDesc">Description</label>
                <textarea name="description" class="form-textarea @error('description') error @enderror" id="catDesc" placeholder="Brief description...">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="catParent">Parent Category</label>
                <select name="parent_id" class="form-select @error('parent_id') error @enderror" id="catParent">
                    <option value="">None (Root Category)</option>
                    @foreach($parentCategories as $pCat)
                        <option value="{{ $pCat->id }}" {{ old('parent_id') == $pCat->id ? 'selected' : '' }}>{{ $pCat->name }}</option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Category Image</label>
                <div class="upload-zone" id="uploadZone">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" id="uploadZoneIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="upload-text" id="uploadZoneText">Click or drag image here</span>
                    <span class="upload-subtext" id="uploadZoneSubtext">PNG, JPG, WEBP up to 2MB</span>
                    <input type="file" name="image" class="upload-input @error('image') error @enderror" id="catImage" accept="image/*">
                    <img id="imagePreview" class="upload-preview" style="display: none;" alt="Preview image">
                    <button type="button" class="upload-remove" id="uploadRemove" aria-label="Remove image" style="display: none;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                </div>
                @error('image')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="catSort">Sort Order</label>
                <input type="number" name="sort_order" class="form-input @error('sort_order') error @enderror" id="catSort" placeholder="1" min="0" value="{{ old('sort_order', 1) }}">
                @error('sort_order')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="toggle-wrap">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} style="display: none;">
                    <div class="toggle {{ old('is_active', '1') == '1' ? 'active' : '' }}" id="catStatusToggle" role="switch" aria-checked="{{ old('is_active', '1') == '1' ? 'true' : 'false' }}" tabindex="0"><div class="toggle-knob"></div></div>
                    <span class="toggle-label" id="catStatusLabel">{{ old('is_active', '1') == '1' ? 'Active' : 'Inactive' }}</span>
                </div>
                @error('is_active')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>
        </form>
    </div>
    
    <div class="modal-footer">
        <button type="button" class="btn-cancel" id="btnCancel">Cancel</button>
        <button type="submit" form="categoryForm" class="btn-save" id="btnSave">Save Category</button>
    </div>
</div>

<!-- Custom Delete Confirmation Modal Overlay -->
<div class="modal-overlay" id="deleteOverlay"></div>
<div class="modal modal-delete" id="deleteModal">
    <form id="deleteForm" method="POST" action="">
        @csrf
        @method('DELETE')
        <div class="modal-body" style="padding-top:24px;text-align:center;">
            <div class="delete-icon-wrap">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="delete-title">Delete Category?</h3>
            <p class="delete-text" id="deleteText">This action cannot be undone. All products in this category will be affected.</p>
        </div>
        <div class="delete-footer">
            <button type="button" class="btn-cancel" id="btnCancelDelete" onclick="closeDeleteModal()">Cancel</button>
            <button type="submit" class="btn-delete" id="btnConfirmDelete">Delete Permanently</button>
        </div>
    </form>
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

        // --- Create / Edit Modal logic ---
        const modalOverlay = document.getElementById('modalOverlay');
        const categoryModal = document.getElementById('categoryModal');
        const categoryForm = document.getElementById('categoryForm');
        const formMethod = document.getElementById('formMethod');
        const editIdInput = document.getElementById('editIdInput');
        
        const modalTitle = document.getElementById('modalTitle');
        const btnSave = document.getElementById('btnSave');
        const modalClose = document.getElementById('modalClose');
        const btnCancel = document.getElementById('btnCancel');

        const catName = document.getElementById('catName');
        const catSlug = document.getElementById('catSlug');
        const catDesc = document.getElementById('catDesc');
        const catParent = document.getElementById('catParent');
        const catSort = document.getElementById('catSort');
        const catImage = document.getElementById('catImage');
        const isActiveInput = document.getElementById('isActiveInput');
        const catStatusToggle = document.getElementById('catStatusToggle');
        const catStatusLabel = document.getElementById('catStatusLabel');

        const uploadZone = document.getElementById('uploadZone');
        const uploadRemove = document.getElementById('uploadRemove');
        const imagePreview = document.getElementById('imagePreview');
        const uploadZoneIcon = document.getElementById('uploadZoneIcon');
        const uploadZoneText = document.getElementById('uploadZoneText');
        const uploadZoneSubtext = document.getElementById('uploadZoneSubtext');

        let isSlugManuallyEdited = false;

        function resetUploadZone() {
            if (uploadZoneIcon) uploadZoneIcon.style.display = 'block';
            if (uploadZoneText) uploadZoneText.style.display = 'block';
            if (uploadZoneSubtext) uploadZoneSubtext.style.display = 'block';
            if (imagePreview) {
                imagePreview.style.display = 'none';
                imagePreview.src = '';
            }
            if (uploadRemove) uploadRemove.style.display = 'none';
            if (catImage) catImage.value = '';
        }

        function showImagePreview(src) {
            if (uploadZoneIcon) uploadZoneIcon.style.display = 'none';
            if (uploadZoneText) uploadZoneText.style.display = 'none';
            if (uploadZoneSubtext) uploadZoneSubtext.style.display = 'none';
            if (imagePreview) {
                imagePreview.src = src;
                imagePreview.style.display = 'block';
            }
            if (uploadRemove) uploadRemove.style.display = 'flex';
        }

        window.openAddModal = function(preserveOld = false) {
            modalTitle.textContent = 'Add Category';
            btnSave.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Category';
            categoryForm.setAttribute('action', "{{ route('admin.categories.store') }}");
            formMethod.value = 'POST';
            editIdInput.value = '';
            isSlugManuallyEdited = false;

            if (!preserveOld) {
                categoryForm.reset();
                resetUploadZone();
                
                // Set default status active
                catStatusToggle.classList.add('active');
                catStatusLabel.textContent = 'Active';
                catStatusToggle.setAttribute('aria-checked', 'true');
                isActiveInput.checked = true;
                catSort.value = 1;
            }

            // Show all parent options
            Array.from(catParent.options).forEach(opt => {
                opt.style.display = '';
            });

            modalOverlay.classList.add('active');
            categoryModal.classList.add('active');
        };

        window.openEditModal = function(button, preserveOld = false) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const slug = button.getAttribute('data-slug');
            const description = button.getAttribute('data-description');
            const parentId = button.getAttribute('data-parent-id');
            const sortOrder = button.getAttribute('data-sort-order');
            const isActive = button.getAttribute('data-is-active') === '1';
            const image = button.getAttribute('data-image');

            modalTitle.textContent = 'Edit Category';
            btnSave.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Update Category';
            categoryForm.setAttribute('action', `/admin/categories/${id}`);
            formMethod.value = 'PUT';
            editIdInput.value = id;
            isSlugManuallyEdited = true;

            if (!preserveOld) {
                catName.value = name;
                catSlug.value = slug;
                catDesc.value = description;
                catParent.value = parentId || '';
                catSort.value = sortOrder || 1;

                if (image) {
                    showImagePreview(image);
                } else {
                    resetUploadZone();
                }

                if (isActive) {
                    catStatusToggle.classList.add('active');
                    catStatusLabel.textContent = 'Active';
                    catStatusToggle.setAttribute('aria-checked', 'true');
                    isActiveInput.checked = true;
                } else {
                    catStatusToggle.classList.remove('active');
                    catStatusLabel.textContent = 'Inactive';
                    catStatusToggle.setAttribute('aria-checked', 'false');
                    isActiveInput.checked = false;
                }
            }

            // Exclude self from parent select options
            Array.from(catParent.options).forEach(opt => {
                if (opt.value === id) {
                    opt.style.display = 'none';
                } else {
                    opt.style.display = '';
                }
            });

            modalOverlay.classList.add('active');
            categoryModal.classList.add('active');
        };

        window.closeModal = function() {
            modalOverlay.classList.remove('active');
            categoryModal.classList.remove('active');
        };

        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (btnCancel) btnCancel.addEventListener('click', closeModal);

        // Click on overlay to close
        if (modalOverlay) {
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });
        }

        // Escape key to close modals
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
            }
        });

        // Status Toggle Switch Interaction
        if (catStatusToggle && catStatusLabel && isActiveInput) {
            catStatusToggle.addEventListener('click', () => {
                const isActive = catStatusToggle.classList.toggle('active');
                catStatusLabel.textContent = isActive ? 'Active' : 'Inactive';
                catStatusToggle.setAttribute('aria-checked', isActive);
                isActiveInput.checked = isActive;
            });
            catStatusToggle.addEventListener('keydown', (e) => {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    catStatusToggle.click();
                }
            });
        }

        // Drag & Drop Image Upload Zone Interaction
        if (catImage) {
            catImage.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 2 * 1024 * 1024) {
                    alert('Image must be less than 2MB.');
                    catImage.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = (ev) => {
                    showImagePreview(ev.target.result);
                };
                reader.readAsDataURL(file);
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
                const file = e.dataTransfer.files[0];
                if (file && file.type.startsWith('image/')) {
                    catImage.files = e.dataTransfer.files;
                    catImage.dispatchEvent(new Event('change'));
                }
            });
        }

        if (uploadRemove) {
            uploadRemove.addEventListener('click', (e) => {
                e.stopPropagation();
                resetUploadZone();
            });
        }

        // Automatic slug generator
        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')
                .replace(/[^\w\-]+/g, '')
                .replace(/\-\-+/g, '-')
                .replace(/^-+/, '')
                .replace(/-+$/, '');
        }

        if (catName && catSlug) {
            catName.addEventListener('input', function() {
                if (!isSlugManuallyEdited) {
                    catSlug.value = slugify(catName.value);
                }
            });

            catSlug.addEventListener('input', function() {
                isSlugManuallyEdited = catSlug.value.length > 0;
            });
        }
        
        // Validation failed auto-open handler
        @if($errors->any())
        const wasEditing = "{{ old('_method') }}" === "PUT";
        const oldId = "{{ old('edit_id') }}";
        if (wasEditing && oldId) {
            const editBtn = document.querySelector(`.action-btn.edit[data-id="${oldId}"]`);
            if (editBtn) {
                openEditModal(editBtn, true);
            } else {
                openAddModal(true);
            }
        } else {
            openAddModal(true);
        }
        @endif
    })();

    // --- Delete Modal logic ---
    const deleteOverlay = document.getElementById('deleteOverlay');
    const deleteModal = document.getElementById('deleteModal');
    const deleteForm = document.getElementById('deleteForm');
    const deleteText = document.getElementById('deleteText');

    window.confirmDelete = function(id, name) {
        if (!deleteOverlay || !deleteForm || !deleteText) return;
        deleteForm.setAttribute('action', `/admin/categories/${id}`);
        deleteText.innerHTML = `Are you sure you want to delete category <strong>"${name}"</strong>? This action cannot be undone and will fail if products or subcategories remain attached.`;
        deleteOverlay.classList.add('active');
        deleteModal.classList.add('active');
        document.body.style.overflow = 'hidden';
    };

    window.closeDeleteModal = function() {
        if (!deleteOverlay) return;
        deleteOverlay.classList.remove('active');
        deleteModal.classList.remove('active');
        document.body.style.overflow = '';
    };

    if (deleteOverlay) {
        deleteOverlay.addEventListener('click', function(e) {
            if (e.target === deleteOverlay) {
                closeDeleteModal();
            }
        });
    }
</script>
@endsection
