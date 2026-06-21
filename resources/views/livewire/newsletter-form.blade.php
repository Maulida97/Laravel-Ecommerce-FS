<div>
    @if ($successMessage)
        <div class="newsletter-success-box alert alert-success" style="color: white; background: var(--success); padding: var(--space-4); border-radius: var(--radius-lg); margin-top: var(--space-4); font-weight: var(--font-semibold); text-align: center;">
            {{ $successMessage }}
        </div>
    @else
        <form wire:submit.prevent="subscribe" class="newsletter-form">
            <div style="flex: 1; display: flex; flex-direction: column; align-items: flex-start; gap: var(--space-1); width: 100%;">
                <input type="email" wire:model="email" class="newsletter-input" placeholder="Enter your email" style="width: 100%;" required>
                @error('email')
                    <span class="newsletter-error" style="color: #F87171; font-size: var(--text-xs); font-weight: var(--font-medium); margin-top: var(--space-1); display: block; text-align: left;">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit" class="newsletter-btn">Subscribe</button>
        </form>
    @endif
</div>
