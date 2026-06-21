<div>
    @section('title', 'My Profile — Tokoku.id')

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <h1 class="font-bold text-3xl text-text-primary mb-2">User Profile</h1>
            <p class="text-base text-text-secondary">Manage your personal information and account settings.</p>
        </div>
        <div>
            <button wire:click="toggleEdit" class="btn {{ $isEditing ? 'btn-secondary' : 'btn-primary' }} flex items-center gap-2 cursor-pointer">
                <span class="material-symbols-outlined text-[18px]">{{ $isEditing ? 'close' : 'edit' }}</span>
                {{ $isEditing ? 'Cancel Edit' : 'Edit Profile' }}
            </button>
        </div>
    </div>

    @if ($successMessage)
        <div class="alert-success" style="background: var(--success); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
            {{ $successMessage }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Identity / Edit Card -->
        <div class="md:col-span-2 card" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-8); box-shadow: var(--shadow-sm);">
            
            @if(!$isEditing)
                <!-- View Mode -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 mb-8 text-center sm:text-left">
                    <div class="relative group">
                        <div class="w-24 h-24 rounded-full overflow-hidden border-4 border-primary-100 flex items-center justify-center">
                            @if(auth()->user()->avatar)
                                <img class="w-full h-full object-cover" src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}">
                            @else
                                <div class="w-full h-full bg-primary-600 text-white flex items-center justify-center font-bold text-3xl">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <h2 class="font-bold text-2xl text-text-primary">{{ auth()->user()->name }}</h2>
                        <p class="text-primary-500 font-semibold mt-1">Member since {{ auth()->user()->created_at->format('M Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <label class="block font-semibold text-xs text-text-muted uppercase tracking-wider mb-2 font-label-caps">Email Address</label>
                        <div class="flex items-center gap-3 text-text-primary">
                            <span class="material-symbols-outlined text-primary-500/60">mail</span>
                            <span class="text-base font-medium">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold text-xs text-text-muted uppercase tracking-wider mb-2 font-label-caps">Phone Number</label>
                        <div class="flex items-center gap-3 text-text-primary">
                            <span class="material-symbols-outlined text-primary-500/60">call</span>
                            <span class="text-base font-medium">{{ auth()->user()->phone ?? 'Not set' }}</span>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-xs text-text-muted uppercase tracking-wider mb-2 font-label-caps">Delivery Address</label>
                        <div class="flex items-start gap-3 text-text-primary">
                            <span class="material-symbols-outlined text-primary-500/60 mt-0.5">home_pin</span>
                            <p class="text-base font-medium leading-relaxed">{{ auth()->user()->address ?? 'No address saved yet.' }}</p>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block font-semibold text-xs text-text-muted uppercase tracking-wider mb-2 font-label-caps">Biography</label>
                        <div class="flex items-start gap-3 text-text-primary">
                            <span class="material-symbols-outlined text-primary-500/60 mt-0.5">description</span>
                            <p class="text-base font-medium leading-relaxed">{{ auth()->user()->bio ?? 'No biography added yet.' }}</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Edit Mode -->
                <form wire:submit.prevent="updateProfile">
                    <h3 class="font-bold text-xl text-text-primary mb-6">Edit Profile Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="form-group full-width" style="display: flex; flex-direction: column; gap: var(--space-2); grid-column: span 2;">
                            <label for="name" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Full Name</label>
                            <input type="text" id="name" wire:model.defer="name" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                            @error('name') <span class="error-msg" style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                            <label for="email" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Email Address</label>
                            <input type="email" id="email" wire:model.defer="email" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                            @error('email') <span class="error-msg" style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                            <label for="phone" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Phone Number</label>
                            <input type="text" id="phone" wire:model.defer="phone" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);">
                            @error('phone') <span class="error-msg" style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group full-width" style="display: flex; flex-direction: column; gap: var(--space-2); grid-column: span 2;">
                            <label for="address" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Delivery Address</label>
                            <textarea id="address" wire:model.defer="address" class="form-input" style="width: 100%; min-height: 80px; padding: var(--space-3) var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary); outline: none;"></textarea>
                            @error('address') <span class="error-msg" style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group full-width" style="display: flex; flex-direction: column; gap: var(--space-2); grid-column: span 2;">
                            <label for="bio" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Biography</label>
                            <textarea id="bio" wire:model.defer="bio" class="form-input" style="width: 100%; min-height: 80px; padding: var(--space-3) var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary); outline: none;"></textarea>
                            @error('bio') <span class="error-msg" style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div style="margin-top: var(--space-6); display: flex; gap: var(--space-3); justify-content: flex-end;">
                        <button type="button" wire:click="toggleEdit" class="btn btn-secondary cursor-pointer">Cancel</button>
                        <button type="submit" class="btn btn-primary cursor-pointer">Save Changes</button>
                    </div>
                </form>
            @endif

        </div>

        <!-- Sidebar Info Column -->
        <div class="flex flex-col gap-6">
            <!-- Account Status Card -->
            @php
                $filledFields = 0;
                if (!empty(auth()->user()->name)) $filledFields++;
                if (!empty(auth()->user()->email)) $filledFields++;
                if (!empty(auth()->user()->phone)) $filledFields++;
                if (!empty(auth()->user()->address)) $filledFields++;
                if (!empty(auth()->user()->bio)) $filledFields++;
                $profilePercent = round(($filledFields / 5) * 100);
            @endphp
            <div class="card" style="background: var(--primary-600); color: white; border: none; padding: var(--space-6); border-radius: var(--radius-2xl);">
                <h3 class="text-xs uppercase font-semibold tracking-wider font-label-caps opacity-80 mb-4">Account Status</h3>
                <div class="flex items-center gap-3 mb-4">
                    <span class="material-symbols-outlined text-[32px]">verified_user</span>
                    <span class="font-bold text-xl">Verified Member</span>
                </div>
                <div style="width: 100%; bg: rgba(255,255,255,0.2); background: rgba(255,255,255,0.2); height: 8px; border-radius: var(--radius-full); overflow: hidden; margin-bottom: var(--space-2);">
                    <div style="background: white; height: 100%; width: {{ $profilePercent }}%;"></div>
                </div>
                <p class="text-xs opacity-80 font-medium">{{ $profilePercent }}% Profile completed</p>
            </div>

            <!-- Profile Quick Links Card -->
            <div class="card" style="background: var(--bg-primary); border: 1px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-6);">
                <h3 class="text-xs uppercase font-semibold tracking-wider font-label-caps text-text-muted mb-4">Portal Navigation</h3>
                <ul class="space-y-4" style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <li>
                        <a class="flex items-center justify-between group text-text-primary hover:text-primary-600 transition-colors font-medium text-sm" href="{{ route('dashboard.orders') }}">
                            <span>View All Orders</span>
                            <span class="material-symbols-outlined text-text-muted">chevron_right</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center justify-between group text-text-primary hover:text-primary-600 transition-colors font-medium text-sm" href="{{ route('dashboard.wishlist') }}">
                            <span>View Your Wishlist</span>
                            <span class="material-symbols-outlined text-text-muted">chevron_right</span>
                        </a>
                    </li>
                    <li>
                        <a class="flex items-center justify-between group text-text-primary hover:text-primary-600 transition-colors font-medium text-sm" href="{{ route('dashboard.address-book') }}">
                            <span>Address Book</span>
                            <span class="material-symbols-outlined text-text-muted">chevron_right</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mt-12">
        <h2 class="font-bold text-2xl text-text-primary mb-6">Recent Transactions</h2>
        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
            @if($recentOrders->isNotEmpty())
                @foreach($recentOrders as $order)
                    <!-- Order Card -->
                    <div class="flex items-center justify-between p-5 bg-bg-primary border border-border rounded-xl hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center text-primary-600">
                                <span class="material-symbols-outlined">shopping_bag</span>
                            </div>
                            <div>
                                <p class="font-bold text-text-primary text-base">Order #{{ $order->order_number }}</p>
                                <p class="text-xs text-text-secondary mt-1">{{ $order->items->count() }} items • {{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right" style="display: flex; flex-direction: column; align-items: flex-end; gap: var(--space-1);">
                            <p class="font-bold text-text-primary text-base">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                            <span style="display: inline-block; padding: 2px 8px; border-radius: var(--radius-full); font-size: 10px; font-weight: var(--font-bold); text-transform: uppercase; background: {{ $order->order_status === 'delivered' ? 'rgba(16, 185, 129, 0.15)' : ($order->order_status === 'cancelled' ? 'rgba(239, 68, 68, 0.15)' : 'rgba(99, 102, 241, 0.15)') }}; color: {{ $order->order_status === 'delivered' ? 'var(--success)' : ($order->order_status === 'cancelled' ? 'var(--danger)' : 'var(--primary-500)') }};">
                                {{ $order->order_status }}
                            </span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="card text-center" style="padding: var(--space-12) 0; color: var(--text-secondary); display: flex; flex-direction: column; align-items: center; gap: var(--space-3);">
                    <div style="width: 64px; height: 64px; background: var(--primary-50); color: var(--primary-500); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center;">
                        <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                    </div>
                    <span style="font-weight: var(--font-medium);">You have not placed any orders yet.</span>
                </div>
            @endif
        </div>
    </div>
</div>
