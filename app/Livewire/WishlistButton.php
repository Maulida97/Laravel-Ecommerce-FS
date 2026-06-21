<?php

namespace App\Livewire;

use Livewire\Component;

class WishlistButton extends Component
{
    public int $productId;
    public string $layout = 'icon'; // 'icon' or 'button'
    public bool $isLiked = false;

    protected $listeners = ['wishlist-updated' => 'checkStatus'];

    /**
     * Mount component.
     */
    public function mount(int $productId, string $layout = 'icon')
    {
        $this->productId = $productId;
        $this->layout = $layout;
        $this->checkStatus();
    }

    /**
     * Check current wishlist status.
     */
    public function checkStatus()
    {
        if (auth()->check()) {
            $this->isLiked = auth()->user()->wishlistItems()->where('product_id', $this->productId)->exists();
        } else {
            $this->isLiked = false;
        }
    }

    /**
     * Toggle wishlist item status.
     */
    public function toggle()
    {
        if (!auth()->check()) {
            return $this->redirect(route('login'));
        }

        $user = auth()->user();
        $item = $user->wishlistItems()->where('product_id', $this->productId)->first();

        if ($item) {
            $item->delete();
            $this->isLiked = false;
        } else {
            $user->wishlistItems()->create(['product_id' => $this->productId]);
            $this->isLiked = true;
        }

        $this->dispatch('wishlist-updated');
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }
}
