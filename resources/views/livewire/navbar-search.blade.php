<div class="search-container" x-data="{ open: false }" @click.away="open = false" style="position: relative; width: 260px;">
    <div class="search-input-wrapper" style="position: relative; width: 100%;">
        <input type="text" 
               wire:model.live.debounce.250ms="query" 
               placeholder="Search premium products..." 
               class="search-input"
               @focus="open = true"
               wire:keydown.enter="submitSearch"
               wire:keydown.escape="clearSearch"
               style="width: 100%; padding: var(--space-2) var(--space-8) var(--space-2) var(--space-4); border: 1px solid var(--border); border-radius: var(--radius-lg); font-size: var(--text-sm); outline: none; background: var(--bg-secondary); color: var(--text-primary); transition: all 0.2s;"
        >
        <span class="search-icon" style="position: absolute; right: var(--space-3); top: 50%; transform: translateY(-50%); color: var(--text-secondary); pointer-events: none; display: flex; align-items: center;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </span>
    </div>

    <div class="search-dropdown" 
         x-show="open && $wire.query.length >= 3" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         style="position: absolute; top: calc(100% + var(--space-2)); left: 0; right: 0; background: white; border: 1px solid var(--border); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: 1100; max-height: 320px; overflow-y: auto; padding: var(--space-2) 0; display: none;"
    >
        @if(count($suggestions) > 0)
            <div class="search-suggestions-list" style="display: flex; flex-direction: column;">
                @foreach($suggestions as $product)
                    <div wire:click="selectProduct('{{ addslashes($product['name']) }}')" class="search-suggestion-item" style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-2) var(--space-4); cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='var(--bg-secondary)'" onmouseout="this.style.background='transparent'">
                        <img src="{{ $product['primary_image_url'] }}" alt="{{ $product['name'] }}" style="width: 36px; height: 36px; border-radius: var(--radius-md); object-fit: cover;">
                        <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; text-align: left;">
                            <span style="font-size: var(--text-sm); font-weight: var(--font-semibold); color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product['name'] }}</span>
                            <span style="font-size: var(--text-xs); color: var(--primary-600); font-weight: var(--font-medium); text-transform: uppercase;">{{ $product['category']['name'] ?? 'Premium' }}</span>
                        </div>
                        <span style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-primary); white-space: nowrap;">Rp {{ number_format($product['price'], 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="search-no-results" style="padding: var(--space-4) var(--space-4); text-align: center; color: var(--text-muted); font-size: var(--text-sm);">
                No products found for "{{ $query }}"
            </div>
        @endif
    </div>
</div>
