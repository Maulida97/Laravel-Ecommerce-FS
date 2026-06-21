<div>
    @if($layout === 'button')
        <button type="button" wire:click.prevent="toggle" class="btn flex items-center justify-center gap-2" 
                style="height: 48px; border: 1.5px solid var(--border); padding: 0 var(--space-6); border-radius: var(--radius-lg); font-weight: var(--font-bold); background: {{ $isLiked ? 'rgba(239, 68, 68, 0.1)' : 'transparent' }}; color: {{ $isLiked ? 'var(--danger)' : 'var(--text-primary)' }}; cursor: pointer; transition: all 0.2s; border-color: {{ $isLiked ? 'var(--danger)' : 'var(--border)' }};" 
                aria-label="Add to wishlist">
            <svg fill="{{ $isLiked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" style="width: 20px; height: 20px; {{ $isLiked ? 'fill: var(--danger); color: var(--danger);' : 'stroke: currentColor;' }}">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span>{{ $isLiked ? 'Saved to Wishlist' : 'Add to Wishlist' }}</span>
        </button>
    @else
        <button type="button" wire:click.prevent="toggle" class="product-action-btn" style="{{ $isLiked ? 'background: var(--danger); color: white;' : '' }}" aria-label="Add to wishlist">
            <svg fill="{{ $isLiked ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" style="{{ $isLiked ? 'fill: white;' : '' }} width: 20px; height: 20px;">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
        </button>
    @endif
</div>
