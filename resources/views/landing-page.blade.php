@extends('layouts.app')

@section('title', ($settings['store_name'] ?? 'Tokoku.id') . ' — Premium E-Commerce')
@section('styles')
    @vite(['resources/css/landing.css'])
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-slides" style="position: relative; width: 100%; min-height: 100vh; display: grid;">
            @foreach($slides as $index => $slide)
                <div class="hero-slide {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" style="grid-area: 1/1; opacity: {{ $index === 0 ? '1' : '0' }}; pointer-events: {{ $index === 0 ? 'auto' : 'none' }}; transition: opacity 0.8s ease-in-out; display: flex; align-items: center; width: 100%;">
                    <div class="container hero-inner">
                        <div class="hero-content">
                            <div class="eyebrow">New Collection 2026</div>
                            <h1 class="hero-title">{{ $slide->title }}</h1>
                            <p class="hero-subtitle">{{ $slide->subtitle }}</p>
                            <div class="hero-cta">
                                @if($slide->button_text)
                                    <a href="{{ $slide->button_link ?? '#products' }}" class="btn btn-primary btn-lg">{{ $slide->button_text }}</a>
                                @endif
                                <a href="#features" class="btn btn-secondary btn-lg">Learn More</a>
                            </div>
                            <div class="trust-badges">
                                <div class="trust-badge">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: var(--success);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Free Shipping
                                </div>
                                <div class="trust-badge">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: var(--success);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Secure Payment
                                </div>
                                <div class="trust-badge">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: var(--success);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    24/7 Support
                                </div>
                            </div>
                        </div>
                        <div class="hero-visual">
                            <div class="hero-image-wrapper">
                                <img src="{{ $slide->image }}" alt="{{ $slide->title }}" class="hero-image">
                                @if($index === 0)
                                    <div class="floating-card card-1"><img src="https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=100&h=100&fit=crop" alt="Watch"><div class="floating-card-text"><strong>Smart Watch</strong><span>Rp 2.990.000</span></div></div>
                                    <div class="floating-card card-2"><img src="https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=100&h=100&fit=crop" alt="Headphones"><div class="floating-card-text"><strong>Pro Headphones</strong><span>Rp 1.990.000</span></div></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($slides->count() > 1)
            <div class="slider-controls" style="position: absolute; bottom: var(--space-20); left: 50%; transform: translateX(-50%); display: flex; gap: var(--space-2); z-index: 10;">
                @foreach($slides as $index => $slide)
                    <button class="slider-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}" aria-label="Go to slide {{ $index + 1 }}" style="width: 12px; height: 12px; border-radius: var(--radius-full); background: var(--gray-300); border: 2px solid transparent; transition: all 0.3s; padding: 0; cursor: pointer;"></button>
                @endforeach
            </div>
        @endif

        <a href="#features" class="scroll-indicator"><span>Scroll to explore</span><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg></a>
    </section>



    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Why Choose Us</h2>
                <p class="section-subtitle">We believe in quality, transparency, and exceptional customer service. Here's what sets us apart.</p>
            </div>
            <div class="features-grid stagger-children">
                <div class="feature-card fade-in">
                    <div class="feature-icon icon-shipping"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/></svg></div>
                    <h3 class="feature-title">Free Shipping</h3>
                    <p class="feature-desc">Enjoy complimentary shipping on all orders over Rp 500.000. Fast, reliable delivery to your doorstep within 3-5 business days.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon icon-quality"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg></div>
                    <h3 class="feature-title">Premium Quality</h3>
                    <p class="feature-desc">Every product is carefully selected and quality-tested. We partner with trusted brands to ensure you get only the best.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon icon-support"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
                    <h3 class="feature-title">24/7 Support</h3>
                    <p class="feature-desc">Our dedicated support team is available around the clock to assist you with any questions or concerns you may have.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="products" id="products">
        <div class="container">
            <div class="products-header fade-in">
                <div><h2 class="section-title">Featured Products</h2><p class="section-subtitle">Handpicked favorites from our collection</p></div>
                <a href="{{ route('catalog') }}" class="card-link" style="font-size:var(--text-base);">View All →</a>
            </div>
            <div class="products-grid stagger-children">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories" id="categories">
        <div class="container">
            <div class="section-header fade-in">
                <h2 class="section-title">Shop by Category</h2>
                <p class="section-subtitle">Browse our curated collections</p>
            </div>
            <div class="categories-grid stagger-children">
                @foreach($featuredCategories as $category)
                    <a href="{{ route('catalog', ['categories' => [$category->slug]]) }}" class="category-card">
                        <img src="{{ $category->image ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=300&h=300&fit=crop' }}" alt="{{ $category->name }}" loading="lazy">
                        <div class="category-overlay" style="display:flex; flex-direction:column; justify-content:flex-end; align-items:flex-start; gap:var(--space-1);">
                            <span class="category-name">{{ $category->name }}</span>
                            <span class="category-count" style="font-size: var(--text-xs); color: rgba(255,255,255,0.8);">{{ $category->products_count }} {{ Str::plural('Product', $category->products_count) }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="newsletter" id="newsletter">
        <div class="container">
            <div class="newsletter-inner">
                <h2 class="newsletter-title">Stay in the Loop</h2>
                <p class="newsletter-subtitle">Subscribe to our newsletter for exclusive deals, new arrivals, and insider tips.</p>
                <livewire:newsletter-form />
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';
        
        // Auto-slide Hero Carousel
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;
        let slideInterval;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                if (i === index) {
                    slide.classList.add('active');
                    slide.style.opacity = '1';
                    slide.style.pointerEvents = 'auto';
                } else {
                    slide.classList.remove('active');
                    slide.style.opacity = '0';
                    slide.style.pointerEvents = 'none';
                }
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === index);
                if (i === index) {
                    dot.style.background = 'var(--primary-600)';
                    dot.style.transform = 'scale(1.2)';
                } else {
                    dot.style.background = 'var(--gray-300)';
                    dot.style.transform = '';
                }
            });
            
            currentSlide = index;
        }

        function nextSlide() {
            let next = (currentSlide + 1) % slides.length;
            showSlide(next);
        }

        function startInterval() {
            clearInterval(slideInterval);
            slideInterval = setInterval(nextSlide, 5000);
        }

        if (slides.length > 1) {
            dots.forEach(dot => {
                dot.addEventListener('click', function() {
                    const idx = parseInt(this.getAttribute('data-index'));
                    showSlide(idx);
                    startInterval();
                });
                
                // Set initial styling for inactive dots
                const idx = parseInt(dot.getAttribute('data-index'));
                if (idx !== 0) {
                    dot.style.background = 'var(--gray-300)';
                } else {
                    dot.style.background = 'var(--primary-600)';
                }
            });
            startInterval();
        }
    })();
</script>
@endsection
