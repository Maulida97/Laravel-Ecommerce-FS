@extends('layouts.admin')

@section('title', 'Edit Category — Tokoku.id')

@section('menu-categories-active', 'active')

@section('breadcrumb')
    <span>Categories</span> / <span>Edit</span>
@endsection

@section('styles')
    @vite(['resources/css/categories.css'])
@endsection

@section('content')
<div class="category-container" style="max-width: 600px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">Edit Category</h1>
        <a href="{{ route('admin.categories.index') }}" class="btn-cancel" style="text-decoration: none; gap: 8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to List
        </a>
    </div>

    <div class="table-container" style="padding: 24px; background: var(--bg-primary);">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" id="categoryForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label required" for="name">Category Name</label>
                <input type="text" name="name" class="form-input @error('name') error @enderror" id="name" value="{{ old('name', $category->name) }}" placeholder="e.g. Electronics" required>
                @error('name')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label required" for="slug">Slug</label>
                <input type="text" name="slug" class="form-input @error('slug') error @enderror" id="slug" value="{{ old('slug', $category->slug) }}" placeholder="e.g. electronics">
                @error('slug')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea name="description" class="form-textarea @error('description') error @enderror" id="description" placeholder="Brief description...">{{ old('description', $category->description) }}</textarea>
                @error('description')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="parent_id">Parent Category</label>
                <select name="parent_id" class="form-select @error('parent_id') error @enderror" id="parent_id">
                    <option value="">None (Root Category)</option>
                    @foreach($parentCategories as $parent)
                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="sort_order">Sort Order</label>
                <input type="number" name="sort_order" class="form-input @error('sort_order') error @enderror" id="sort_order" placeholder="1" min="0" value="{{ old('sort_order', $category->sort_order) }}">
                @error('sort_order')
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

            <div class="form-group" style="margin-top: 24px;">
                <div class="toggle-wrap">
                    <input type="checkbox" name="is_active" id="isActiveInput" value="1" {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'checked' : '' }} style="display: none;">
                    <div class="toggle {{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'active' : '' }}" id="catStatusToggle" role="switch" aria-checked="{{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'true' : 'false' }}" tabindex="0"><div class="toggle-knob"></div></div>
                    <span class="toggle-label" id="catStatusLabel">{{ old('is_active', $category->is_active ? '1' : '0') == '1' ? 'Active' : 'Inactive' }}</span>
                </div>
                @error('is_active')
                    <div class="error-msg">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 32px; display: flex; justify-content: flex-end; gap: 12px; border-top: 1px solid var(--border-light); padding-top: 20px;">
                <a href="{{ route('admin.categories.index') }}" class="btn-cancel" style="text-decoration: none;">Cancel</a>
                <button type="submit" class="btn-save">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';

        // 1. Status Toggle interaction
        const catStatusToggle = document.getElementById('catStatusToggle');
        const catStatusLabel = document.getElementById('catStatusLabel');
        const isActiveInput = document.getElementById('isActiveInput');

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

        // 2. Drag & Drop Upload Zone Interaction
        const catImage = document.getElementById('catImage');
        const uploadZone = document.getElementById('uploadZone');
        const uploadRemove = document.getElementById('uploadRemove');
        const imagePreview = document.getElementById('imagePreview');

        const uploadZoneIcon = document.getElementById('uploadZoneIcon');
        const uploadZoneText = document.getElementById('uploadZoneText');
        const uploadZoneSubtext = document.getElementById('uploadZoneSubtext');

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

        // Initialize with existing image if any
        @if($category->image)
            showImagePreview("{{ $category->image }}");
        @endif

        // 3. Automatic slug generator
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        let isSlugManuallyEdited = true; // default to true for Edit form

        function slugify(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start
                .replace(/-+$/, '');            // Trim - from end
        }

        if (nameInput && slugInput) {
            // If the slug is cleared, we allow auto-generation
            slugInput.addEventListener('focus', function() {
                if (slugInput.value.trim() === '') {
                    isSlugManuallyEdited = false;
                }
            });

            nameInput.addEventListener('input', function() {
                if (!isSlugManuallyEdited && slugInput.value.trim() === '') {
                    slugInput.value = slugify(nameInput.value);
                }
            });

            slugInput.addEventListener('input', function() {
                isSlugManuallyEdited = slugInput.value.length > 0;
            });
        }

    })();
</script>
@endsection
