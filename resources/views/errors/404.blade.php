@extends('layouts.app')

@section('title', 'Page Not Found — Tokoku.id')

@section('content')
<style>
    html.dark .error-icon-bg {
        background: rgba(99, 102, 241, 0.15) !important;
        color: var(--primary-400) !important;
    }
</style>

<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; background: var(--bg-secondary); padding: var(--space-8); padding-top: 140px;">
    <div style="max-width: 500px; width: 100%; background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-10); box-shadow: var(--shadow-lg); text-align: center;">
        <div class="error-icon-bg" style="width: 80px; height: 80px; background: var(--primary-50); color: var(--primary-600); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-6); transition: all 0.3s;">
            <svg style="width: 40px; height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <h1 style="font-size: 4rem; font-weight: var(--font-bold); color: var(--primary-600); margin-bottom: var(--space-2); line-height: 1;">404</h1>
        <h2 style="font-size: var(--text-xl); font-weight: var(--font-bold); color: var(--text-primary); margin-bottom: var(--space-4);">Page Not Found</h2>
        <p style="font-size: var(--text-sm); color: var(--text-secondary); line-height: 1.6; margin-bottom: var(--space-8);">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
        </p>
        <a href="{{ route('home') }}" class="btn btn-primary" style="width: 100%; height: 48px; display: inline-flex; align-items: center; justify-content: center;">
            Return to Homepage
        </a>
    </div>
</div>
@endsection
