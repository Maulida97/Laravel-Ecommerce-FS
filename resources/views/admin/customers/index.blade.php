@extends('layouts.admin')

@section('title', 'Customers — Admin Dashboard')

@section('menu-customers-active', 'active')

@section('breadcrumb')
    <span>Customers</span> / <span>List</span>
@endsection

@section('styles')
    @vite(['resources/css/customers.css'])
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
        <h1 class="page-title">Customer List</h1>
        <button type="button" class="btn-add" id="btnAddCustomer" onclick="openAddModal()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Customer
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('admin.customers.index') }}" method="GET" style="display: flex; flex: 1; align-items: center; gap: 12px; flex-wrap: wrap; width: 100%;">
            <div class="search-wrapper">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" name="search" id="filterSearch" placeholder="Search customers by name, email, phone..." value="{{ request('search') }}">
            </div>
            
            <button type="submit" class="btn-filter" id="btnApplyFilter">Search</button>
            
            @if(request()->anyFilled(['search']))
                <a href="{{ route('admin.customers.index') }}" class="btn-clear" id="btnClearFilter">
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
                    <th class="sortable {{ $sort === 'name' ? 'active ' . $direction : '' }}" onclick="sortBy('name')">Name</th>
                    <th>Phone</th>
                    <th class="sortable {{ $sort === 'orders_count' ? 'active ' . $direction : '' }}" onclick="sortBy('orders_count')">Total Orders</th>
                    <th class="sortable {{ $sort === 'total_spent' ? 'active ' . $direction : '' }}" onclick="sortBy('total_spent')">Total Spent</th>
                    <th class="sortable {{ $sort === 'created_at' ? 'active ' . $direction : '' }}" onclick="sortBy('created_at')">Joined Date</th>
                    <th style="width:120px;text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                @forelse($customers as $cust)
                    <tr>
                        <td>
                            <div class="user-avatar-cell">
                                @if($cust->avatar)
                                    <img src="{{ $cust->avatar }}" alt="{{ $cust->name }}" class="cust-avatar">
                                @else
                                    <div class="cust-avatar-placeholder">
                                        {{ strtoupper(substr($cust->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="cust-name">{{ $cust->name }}</div>
                                    <div class="cust-email">{{ $cust->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span>{{ $cust->phone ?: '-' }}</span>
                        </td>
                        <td>
                            <span class="badge-stat">{{ $cust->orders_count }}</span>
                        </td>
                        <td>
                            <span class="badge-stat spent">Rp {{ number_format($cust->total_spent ?? 0, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span>{{ $cust->created_at->format('d M Y') }}</span>
                        </td>
                        <td style="text-align:right;">
                            <div class="actions" style="justify-content: flex-end;">
                                <a href="{{ route('admin.customers.show', $cust->id) }}" class="action-btn view" title="View Statistics">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                </a>
                                <button type="button" 
                                        class="action-btn edit" 
                                        data-id="{{ $cust->id }}"
                                        data-name="{{ $cust->name }}"
                                        data-email="{{ $cust->email }}"
                                        data-phone="{{ $cust->phone }}"
                                        data-address="{{ $cust->address }}"
                                        data-bio="{{ $cust->bio }}"
                                        data-avatar="{{ $cust->avatar }}"
                                        onclick="openEditModal(this)"
                                        title="Edit Customer">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="action-btn delete" onclick="confirmDelete({{ $cust->id }}, '{{ addslashes($cust->name) }}')" title="Delete Customer">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <h3>No customers found</h3>
                                <p>Try adjusting your search query.</p>
                                <button type="button" class="btn-add" onclick="openAddModal()">Add Customer</button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        @if($customers->count() > 0)
            <div class="pagination">
                <div class="pagination-info">
                    Showing {{ $customers->firstItem() }}-{{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </div>
                <div class="pagination-btns">
                    @if($customers->onFirstPage())
                        <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></span>
                    @else
                        <a href="{{ $customers->previousPageUrl() }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></a>
                    @endif

                    @foreach ($customers->getUrlRange(max(1, $customers->currentPage() - 1), min($customers->lastPage(), $customers->currentPage() + 1)) as $page => $url)
                        @if ($page == $customers->currentPage())
                            <span class="page-btn active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="page-btn">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($customers->hasMorePages())
                        <a href="{{ $customers->nextPageUrl() }}" class="page-btn prev-next"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></a>
                    @else
                        <span class="page-btn prev-next disabled"><svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Hidden Form for Sorting -->
    <form id="sortForm" action="{{ route('admin.customers.index') }}" method="GET" style="display:none;">
        <input type="hidden" name="search" value="{{ request('search') }}">
        <input type="hidden" name="sort" id="sortInput" value="{{ $sort }}">
        <input type="hidden" name="direction" id="directionInput" value="{{ $direction }}">
    </form>

    <!-- Add/Edit Modal Overlay -->
    <div class="modal-overlay" id="modalOverlay"></div>
    <div class="modal" id="customerModal">
        <div class="modal-header">
            <h2 class="modal-title" id="modalTitle">Add Customer</h2>
            <button type="button" class="modal-close" id="modalClose" aria-label="Close modal"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data" id="customerForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="edit_id" id="editIdInput" value="{{ old('edit_id') }}">
                
                <div class="form-group">
                    <label class="form-label required" for="custName">Full Name</label>
                    <input type="text" name="name" class="form-input @error('name') error @enderror" id="custName" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required" for="custEmail">Email Address</label>
                    <input type="email" name="email" class="form-input @error('email') error @enderror" id="custEmail" value="{{ old('email') }}" placeholder="e.g. john@example.com" required>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label required" id="passwordLabel" for="custPassword">Password</label>
                    <input type="password" name="password" class="form-input @error('password') error @enderror" id="custPassword" placeholder="Min. 8 characters" required>
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="custPhone">Phone Number</label>
                    <input type="text" name="phone" class="form-input @error('phone') error @enderror" id="custPhone" value="{{ old('phone') }}" placeholder="e.g. +628123456789">
                    @error('phone')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="custBio">Biography</label>
                    <textarea name="bio" class="form-textarea @error('bio') error @enderror" id="custBio" placeholder="Tell us about the customer...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="custAddress">Billing/Shipping Address</label>
                    <textarea name="address" class="form-textarea @error('address') error @enderror" id="custAddress" placeholder="Full address details...">{{ old('address') }}</textarea>
                    @error('address')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Avatar Profile Image</label>
                    <div class="upload-zone" id="uploadZone">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" id="uploadZoneIcon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="upload-text" id="uploadZoneText">Click or drag avatar image here</span>
                        <span class="upload-subtext" id="uploadZoneSubtext">PNG, JPG, WEBP up to 2MB</span>
                        <input type="file" name="avatar" class="upload-input @error('avatar') error @enderror" id="custAvatar" accept="image/*">
                        <img id="imagePreview" class="upload-preview" style="display: none;" alt="Preview image">
                        <button type="button" class="upload-remove" id="uploadRemove" aria-label="Remove image" style="display: none;"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    @error('avatar')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn-cancel" id="btnCancel">Cancel</button>
            <button type="submit" form="customerForm" class="btn-save" id="btnSave">Save Customer</button>
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
                <h3 class="delete-title">Delete Customer?</h3>
                <p class="delete-text" id="deleteText">This action cannot be undone. All customer profile information will be deleted permanently.</p>
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

        // Sorting trigger
        window.sortBy = function(column) {
            const sortForm = document.getElementById('sortForm');
            const sortInput = document.getElementById('sortInput');
            const directionInput = document.getElementById('directionInput');
            
            if (sortInput.value === column) {
                directionInput.value = directionInput.value === 'asc' ? 'desc' : 'asc';
            } else {
                sortInput.value = column;
                directionInput.value = 'asc';
            }
            sortForm.submit();
        };

        // --- Create / Edit Modal logic ---
        const modalOverlay = document.getElementById('modalOverlay');
        const customerModal = document.getElementById('customerModal');
        const customerForm = document.getElementById('customerForm');
        const formMethod = document.getElementById('formMethod');
        const editIdInput = document.getElementById('editIdInput');
        
        const modalTitle = document.getElementById('modalTitle');
        const btnSave = document.getElementById('btnSave');
        const modalClose = document.getElementById('modalClose');
        const btnCancel = document.getElementById('btnCancel');

        const custName = document.getElementById('custName');
        const custEmail = document.getElementById('custEmail');
        const custPassword = document.getElementById('custPassword');
        const passwordLabel = document.getElementById('passwordLabel');
        const custPhone = document.getElementById('custPhone');
        const custBio = document.getElementById('custBio');
        const custAddress = document.getElementById('custAddress');
        const custAvatar = document.getElementById('custAvatar');

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
            if (custAvatar) custAvatar.value = '';
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
            modalTitle.textContent = 'Add Customer';
            btnSave.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Save Customer';
            customerForm.setAttribute('action', "{{ route('admin.customers.store') }}");
            formMethod.value = 'POST';
            editIdInput.value = '';
            
            // Password required for creation
            custPassword.required = true;
            passwordLabel.classList.add('required');

            if (!preserveOld) {
                customerForm.reset();
                resetUploadZone();
            }

            modalOverlay.classList.add('active');
            customerModal.classList.add('active');
        };

        window.openEditModal = function(button, preserveOld = false) {
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');
            const email = button.getAttribute('data-email');
            const phone = button.getAttribute('data-phone');
            const address = button.getAttribute('data-address');
            const bio = button.getAttribute('data-bio');
            const avatar = button.getAttribute('data-avatar');

            modalTitle.textContent = 'Edit Customer';
            btnSave.innerHTML = '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Update Customer';
            customerForm.setAttribute('action', `/admin/customers/${id}`);
            formMethod.value = 'PUT';
            editIdInput.value = id;
            
            // Password optional for edits
            custPassword.required = false;
            passwordLabel.classList.remove('required');

            if (!preserveOld) {
                custName.value = name;
                custEmail.value = email;
                custPassword.value = '';
                custPhone.value = phone || '';
                custBio.value = bio || '';
                custAddress.value = address || '';

                if (avatar) {
                    showImagePreview(avatar);
                } else {
                    resetUploadZone();
                }
            }

            modalOverlay.classList.add('active');
            customerModal.classList.add('active');
        };

        window.closeModal = function() {
            modalOverlay.classList.remove('active');
            customerModal.classList.remove('active');
        };

        if (modalClose) modalClose.addEventListener('click', closeModal);
        if (btnCancel) btnCancel.addEventListener('click', closeModal);

        if (modalOverlay) {
            modalOverlay.addEventListener('click', (e) => {
                if (e.target === modalOverlay) closeModal();
            });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeDeleteModal();
            }
        });

        // Drag & Drop Image Upload Zone Interaction
        if (custAvatar) {
            custAvatar.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 2 * 1024 * 1024) {
                    alert('Avatar image must be less than 2MB.');
                    custAvatar.value = '';
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
                    custAvatar.files = e.dataTransfer.files;
                    custAvatar.dispatchEvent(new Event('change'));
                }
            });
        }

        if (uploadRemove) {
            uploadRemove.addEventListener('click', (e) => {
                e.stopPropagation();
                resetUploadZone();
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
        deleteForm.setAttribute('action', `/admin/customers/${id}`);
        deleteText.innerHTML = `Are you sure you want to delete customer <strong>"${name}"</strong>? This action cannot be undone. To prevent accounting anomalies, customers with previous order history cannot be deleted.`;
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
