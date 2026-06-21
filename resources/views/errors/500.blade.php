@extends('layouts.app')

@section('title', 'Server Error — Tokoku.id')

@section('content')
<style>
    html.dark .error-icon-bg {
        background: rgba(239, 68, 68, 0.15) !important;
        color: var(--danger) !important;
    }
</style>

<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); padding: var(--space-8); padding-top: 140px;">
    <div style="max-width: 500px; width: 100%; background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-10); box-shadow: var(--shadow-lg); text-align: center;">
        <div class="error-icon-bg" style="width: 80px; height: 80px; background: #fee2e2; color: var(--danger); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-6); transition: all 0.3s;">
            <svg style="width: 40px; height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.172l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
        </div>
        <h1 style="font-size: 4rem; font-weight: var(--font-bold); color: var(--danger); margin-bottom: var(--space-2); line-height: 1;">500</h1>
        <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); color: var(--text-primary); margin-bottom: var(--space-4);">Internal Server Error</h2>
        <p style="font-size: var(--text-sm); color: var(--text-secondary); line-height: 1.6; margin-bottom: var(--space-8);">
            Something went wrong on our servers. We are already looking into it. Please try refreshing the page or check back later.
        </p>
        <div style="display: flex; flex-direction: column; gap: var(--space-3);">
            <button onclick="window.location.reload();" class="btn btn-primary" style="width: 100%; height: 48px; display: inline-flex; align-items: center; justify-content: center;">
                Refresh Page
            </button>
            <a href="{{ route('home') }}" class="btn btn-secondary" style="width: 100%; height: 48px; display: inline-flex; align-items: center; justify-content: center;">
                Return to Homepage
            </a>
        </div>
    </div>
</div>
@endsection
