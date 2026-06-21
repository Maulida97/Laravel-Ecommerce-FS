<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ProfilePage extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $bio = '';

    public bool $isEditing = false;
    public string $successMessage = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'bio' => 'nullable|string|max:500',
    ];

    public function mount()
    {
        if (auth()->user()->isAdmin()) {
            return $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        }

        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->address = $user->address ?? '';
        $this->bio = $user->bio ?? '';
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        $this->successMessage = '';
    }

    public function updateProfile()
    {
        $this->validate();

        $user = auth()->user();
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'address' => $this->address ?: null,
            'bio' => $this->bio ?: null,
        ]);

        $this->isEditing = false;
        $this->successMessage = 'Profile updated successfully!';
    }

    public function render()
    {
        $recentOrders = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('orders')) {
            $recentOrders = Order::where('user_id', auth()->id())
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('livewire.customer.profile-page', [
            'recentOrders' => $recentOrders
        ])->layout('layouts.customer-portal');
    }
}
