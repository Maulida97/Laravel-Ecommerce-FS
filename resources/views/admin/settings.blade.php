@extends('layouts.admin')

@section('title', 'Store Settings — Tokoku.id')

@section('menu-settings-active', 'active')

@section('breadcrumb')
    <span>Admin</span> / <span>Settings</span>
@endsection

@section('styles')
    @vite(['resources/css/settings.css'])
@endsection

@section('content')
<div class="settings-container">
    @if(session('success'))
        <div class="alert-toast" id="successAlert">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="settings-grid-layout">
        <!-- Sticky Navigation Sidebar -->
        <aside class="settings-nav-wrapper">
            <nav class="settings-nav" aria-label="Settings sections">
                <a href="#general" class="settings-nav-btn active" id="btn-general">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <span>General Info</span>
                </a>
                <a href="#contact" class="settings-nav-btn" id="btn-contact">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Contact Info</span>
                </a>
                <a href="#shipping" class="settings-nav-btn" id="btn-shipping">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span>Shipping Settings</span>
                </a>
                <a href="#social" class="settings-nav-btn" id="btn-social">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    <span>Social Media</span>
                </a>
                <a href="#midtrans" class="settings-nav-btn" id="btn-midtrans">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Midtrans API</span>
                </a>
            </nav>
        </aside>

        <!-- Forms Container -->
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" id="settingsForm">
            @csrf
            
            <!-- General Info Section -->
            <section id="general" class="settings-section">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h2 class="settings-card-title">General Information</h2>
                        <p class="settings-card-subtitle">Set up your brand identity and store storefront visuals.</p>
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-group col-span-2">
                            <label class="form-label">Store Logo</label>
                            <div class="logo-upload-wrapper">
                                <div class="logo-preview-box" id="logoPreviewBox">
                                    @if(!empty($settings['store_logo']))
                                        <img src="{{ $settings['store_logo'] }}" alt="Store Logo" id="logoPreviewImg">
                                    @else
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" id="logoPreviewFallback">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="logo-upload-info">
                                    <label for="store_logo" class="logo-upload-btn-label">Choose new image</label>
                                    <input type="file" name="store_logo" id="store_logo" accept="image/*" class="sr-only">
                                    <span class="logo-upload-text">Supports JPG, PNG or WEBP. Max size 2MB.</span>
                                </div>
                            </div>
                            @error('store_logo')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" name="store_name" id="store_name" class="form-input" value="{{ old('store_name', $settings['store_name']) }}" placeholder="e.g. Tokoku.id" required>
                            @error('store_name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="store_tagline" class="form-label">Store Tagline</label>
                            <input type="text" name="store_tagline" id="store_tagline" class="form-input" value="{{ old('store_tagline', $settings['store_tagline']) }}" placeholder="e.g. Premium E-Commerce Platform">
                            @error('store_tagline')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Info Section -->
            <section id="contact" class="settings-section">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h2 class="settings-card-title">Contact Information</h2>
                        <p class="settings-card-subtitle">Define customer contact detail points shown in invoices and help centers.</p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <input type="email" name="contact_email" id="contact_email" class="form-input" value="{{ old('contact_email', $settings['contact_email']) }}" placeholder="e.g. support@tokoku.id" required>
                            </div>
                            @error('contact_email')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <input type="text" name="contact_phone" id="contact_phone" class="form-input" value="{{ old('contact_phone', $settings['contact_phone']) }}" placeholder="e.g. +6281234567890" required>
                            </div>
                            @error('contact_phone')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group col-span-2">
                            <label for="contact_address" class="form-label">Contact Address</label>
                            <textarea name="contact_address" id="contact_address" class="form-textarea" placeholder="Enter physical store location address..." required>{{ old('contact_address', $settings['contact_address']) }}</textarea>
                            @error('contact_address')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Shipping Settings Section -->
            <section id="shipping" class="settings-section">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h2 class="settings-card-title">Shipping Configuration</h2>
                        <p class="settings-card-subtitle">Set up courier default fees and purchase milestones for free shipping.</p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="default_shipping_cost" class="form-label">Default Shipping Cost ($)</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left" style="font-weight:600; font-size:14px; color:var(--text-secondary);">$</span>
                                <input type="number" step="0.01" name="default_shipping_cost" id="default_shipping_cost" class="form-input" value="{{ old('default_shipping_cost', $settings['default_shipping_cost']) }}" placeholder="e.g. 15000.00" required>
                            </div>
                            @error('default_shipping_cost')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="free_shipping_threshold" class="form-label">Free Shipping Threshold ($)</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left" style="font-weight:600; font-size:14px; color:var(--text-secondary);">$</span>
                                <input type="number" step="0.01" name="free_shipping_threshold" id="free_shipping_threshold" class="form-input" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold']) }}" placeholder="e.g. 500000.00" required>
                            </div>
                            @error('free_shipping_threshold')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Social Media Section -->
            <section id="social" class="settings-section">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h2 class="settings-card-title">Social Media Links</h2>
                        <p class="settings-card-subtitle">Link your store storefront to official brand social profiles.</p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="social_twitter" class="form-label">Twitter Profile URL</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <input type="url" name="social_twitter" id="social_twitter" class="form-input" value="{{ old('social_twitter', $settings['social_twitter']) }}" placeholder="https://twitter.com/yourbrand">
                            </div>
                            @error('social_twitter')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="social_instagram" class="form-label">Instagram Profile URL</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <input type="url" name="social_instagram" id="social_instagram" class="form-input" value="{{ old('social_instagram', $settings['social_instagram']) }}" placeholder="https://instagram.com/yourbrand">
                            </div>
                            @error('social_instagram')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="social_facebook" class="form-label">Facebook Profile URL</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <input type="url" name="social_facebook" id="social_facebook" class="form-input" value="{{ old('social_facebook', $settings['social_facebook']) }}" placeholder="https://facebook.com/yourbrand">
                            </div>
                            @error('social_facebook')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Midtrans Section -->
            <section id="midtrans" class="settings-section">
                <div class="settings-card">
                    <div class="settings-card-header">
                        <h2 class="settings-card-title">Midtrans Payment Gateway</h2>
                        <p class="settings-card-subtitle">Connect your checkout transactions to your Midtrans merchant account securely.</p>
                    </div>

                    <div class="form-grid">
                        <div class="form-group col-span-2">
                            <div class="switch-container">
                                <div class="switch-label">
                                    <span class="switch-title">Sandbox Testing Mode</span>
                                    <span class="switch-desc">Toggle off to route transactions directly to production gateway.</span>
                                </div>
                                <label class="switch" for="midtrans_sandbox_mode">
                                    <input type="checkbox" name="midtrans_sandbox_mode" id="midtrans_sandbox_mode" value="true" {{ old('midtrans_sandbox_mode', $settings['midtrans_sandbox_mode']) === 'true' ? 'checked' : '' }}>
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="midtrans_server_key" class="form-label">Server Key</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input type="password" name="midtrans_server_key" id="midtrans_server_key" class="form-input" value="{{ old('midtrans_server_key', $settings['midtrans_server_key']) }}" placeholder="Enter Midtrans Server Key">
                                <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('midtrans_server_key', this)" aria-label="Toggle password visibility">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="eye-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            @error('midtrans_server_key')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="midtrans_client_key" class="form-label">Client Key</label>
                            <div class="input-with-icon">
                                <span class="input-icon-left">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                </span>
                                <input type="password" name="midtrans_client_key" id="midtrans_client_key" class="form-input" value="{{ old('midtrans_client_key', $settings['midtrans_client_key']) }}" placeholder="Enter Midtrans Client Key">
                                <button type="button" class="input-icon-right" onclick="togglePasswordVisibility('midtrans_client_key', this)" aria-label="Toggle password visibility">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="eye-icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            </div>
                            @error('midtrans_client_key')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            <!-- Floating Action Banner -->
            <div class="settings-action-banner">
                <div class="banner-info">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Make sure all configurations are correct before updating checkout pathways.</span>
                </div>
                <button type="submit" class="settings-btn-save">
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
    // 1. Password reveal toggle helper
    function togglePasswordVisibility(fieldId, button) {
        const input = document.getElementById(fieldId);
        if (!input) return;
        
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        // Toggle icon visual states
        const eyeIcon = button.querySelector('.eye-icon');
        if (type === 'text') {
            // eye-off icon
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
        } else {
            // eye icon
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        }
    }

    (function() {
        'use strict';

        // 2. Auto-fadeout for alerts
        const successAlert = document.getElementById('successAlert');
        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                successAlert.style.opacity = '0';
                successAlert.style.transform = 'translateY(-10px)';
                setTimeout(() => successAlert.remove(), 500);
            }, 4000);
        }

        // 3. Image file input preview helper
        const logoInput = document.getElementById('store_logo');
        const logoPreviewBox = document.getElementById('logoPreviewBox');

        if (logoInput && logoPreviewBox) {
            logoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // clear existing fallback or image
                        logoPreviewBox.innerHTML = `<img src="${e.target.result}" alt="Store Logo Preview" id="logoPreviewImg">`;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // 4. Scroll Spy navigation script
        const sections = document.querySelectorAll('section.settings-section');
        const navButtons = document.querySelectorAll('.settings-nav-btn');

        function updateScrollSpy() {
            let currentActiveSectionId = '';
            const scrollPos = window.scrollY || document.documentElement.scrollTop;

            sections.forEach(sec => {
                // section offset with threshold
                const offsetTop = sec.offsetTop - 150;
                if (scrollPos >= offsetTop) {
                    currentActiveSectionId = sec.getAttribute('id');
                }
            });

            if (currentActiveSectionId) {
                navButtons.forEach(btn => {
                    btn.classList.remove('active');
                    if (btn.getAttribute('href') === `#${currentActiveSectionId}`) {
                        btn.classList.add('active');
                    }
                });
            }
        }

        window.addEventListener('scroll', updateScrollSpy);
        updateScrollSpy();

        // 5. Smooth scroll click handler
        navButtons.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSec = document.querySelector(targetId);
                
                if (targetSec) {
                    window.scrollTo({
                        top: targetSec.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

    })();
</script>
@endsection
