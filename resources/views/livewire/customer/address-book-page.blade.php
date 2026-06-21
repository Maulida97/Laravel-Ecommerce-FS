<div>
    @section('title', 'Address Book — Tokoku.id')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <nav class="flex items-center gap-2 text-text-secondary text-xs mb-2">
                <a class="hover:text-primary-600" href="{{ route('dashboard.profile') }}">Account</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary-600 font-bold">Address Book</span>
            </nav>
            <h1 class="font-bold text-3xl text-text-primary mb-2">Address Book</h1>
            <p class="text-base text-text-secondary">Manage your shipping and billing addresses for faster checkout.</p>
        </div>
        @if(!$isFormOpen)
            <div>
                <button wire:click="openCreateForm" class="flex items-center justify-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-bold shadow-md hover:shadow-lg transition-all active:scale-95 group cursor-pointer">
                    <span class="material-symbols-outlined group-hover:rotate-90 transition-transform">add</span>
                    <span>Add New Address</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Alerts -->
    @if ($successMessage)
        <div class="alert-success" style="background: var(--success); color: white; padding: var(--space-4); border-radius: var(--radius-lg); margin-bottom: var(--space-6); text-align: left; font-weight: var(--font-semibold);">
            {{ $successMessage }}
        </div>
    @endif

    <!-- Form Section (When open) -->
    @if($isFormOpen)
        <div class="card mb-8 animate-fade-in" style="background: var(--bg-primary); border: 1.5px solid var(--border); border-radius: var(--radius-2xl); padding: var(--space-8); box-shadow: var(--shadow-sm);">
            <h3 class="font-bold text-xl text-text-primary mb-6">{{ $addressId ? 'Edit Address Details' : 'Add New Delivery Address' }}</h3>
            <form wire:submit.prevent="saveAddress">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <label for="label" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Address Label (e.g. Home, Office)</label>
                        <input type="text" id="label" wire:model.defer="label" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required placeholder="Home, Office, Parents' House...">
                        @error('label') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <label for="recipient_name" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Recipient Name</label>
                        <input type="text" id="recipient_name" wire:model.defer="recipient_name" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                        @error('recipient_name') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <label for="phone" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Recipient Phone Number</label>
                        <input type="text" id="phone" wire:model.defer="phone" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                        @error('phone') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <label for="city" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">City</label>
                        <input type="text" id="city" wire:model.defer="city" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                        @error('city') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2);">
                        <label for="postal_code" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Postal Code</label>
                        <input type="text" id="postal_code" wire:model.defer="postal_code" class="form-input" style="width: 100%; height: 44px; padding: 0 var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary);" required>
                        @error('postal_code') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group flex items-center gap-3" style="align-items: center; display: flex; padding-top: var(--space-6);">
                        <input type="checkbox" id="is_default" wire:model.defer="is_default" class="rounded text-primary-600 border-border focus:ring-primary-600 w-5 h-5 cursor-pointer">
                        <label for="is_default" class="text-sm font-semibold text-text-primary cursor-pointer select-none">Set as default address</label>
                    </div>

                    <div class="form-group" style="display: flex; flex-direction: column; gap: var(--space-2); grid-column: span 2;">
                        <label for="address_line" style="font-size: var(--text-xs); font-weight: var(--font-bold); color: var(--text-secondary); text-transform: uppercase;">Full Address Details</label>
                        <textarea id="address_line" wire:model.defer="address_line" class="form-input" style="width: 100%; min-height: 80px; padding: var(--space-3) var(--space-4); border: 1.5px solid var(--border); border-radius: var(--radius-lg); background: var(--bg-primary); color: var(--text-primary); outline: none;" required placeholder="Street name, building/house number, district..."></textarea>
                        @error('address_line') <span style="color: var(--danger); font-size: var(--text-xs);">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div style="margin-top: var(--space-6); display: flex; gap: var(--space-3); justify-content: flex-end;">
                    <button type="button" wire:click="closeForm" class="btn btn-secondary cursor-pointer">Cancel</button>
                    <button type="submit" class="btn btn-primary cursor-pointer">Save Address</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Address Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($addresses as $address)
            @php
                $isDefault = $address->is_default;
                $labelLower = strtolower($address->label);
                $icon = 'location_on';
                if (str_contains($labelLower, 'home')) {
                    $icon = 'home';
                } elseif (str_contains($labelLower, 'office') || str_contains($labelLower, 'work') || str_contains($labelLower, 'business')) {
                    $icon = 'business';
                }
            @endphp
            <!-- Address Card -->
            <div class="bg-bg-primary border {{ $isDefault ? 'border-primary-500 ring-2 ring-primary-500/10' : 'border-border' }} rounded-xl p-6 shadow-xs relative overflow-hidden transition-all hover:-translate-y-1 hover:shadow-md">
                @if($isDefault)
                    <div class="absolute top-0 right-0">
                        <span class="bg-primary-600 text-white text-[9px] font-bold px-3 py-1 rounded-bl-xl uppercase tracking-widest font-label-caps">Default</span>
                    </div>
                @endif
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $isDefault ? 'bg-primary-50 dark:bg-primary-950/20 text-primary-600' : 'bg-gray-50 dark:bg-gray-800 text-text-secondary' }} flex items-center justify-center">
                            <span class="material-symbols-outlined">{{ $icon }}</span>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-text-primary">{{ $address->label }}</h3>
                            <p class="text-xs text-text-secondary mt-0.5 font-medium">{{ $address->recipient_name }}</p>
                        </div>
                    </div>
                    <div class="flex gap-1">
                        <button type="button" wire:click="openEditForm({{ $address->id }})" class="p-2 text-text-muted hover:text-text-primary hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors cursor-pointer" title="Edit">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </button>
                        <button type="button" wire:click="deleteAddress({{ $address->id }})" wire:confirm="Are you sure you want to delete this address?" class="p-2 text-text-muted hover:text-danger hover:bg-gray-50 dark:hover:bg-gray-800 rounded-full transition-colors cursor-pointer" title="Delete">
                            <span class="material-symbols-outlined text-[20px]">delete</span>
                        </button>
                    </div>
                </div>
                <div class="space-y-3 text-sm text-text-secondary">
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-[18px] mt-0.5 text-text-muted">location_on</span>
                        <p class="leading-relaxed">{{ $address->address_line }}, {{ $address->city }} {{ $address->postal_code }}</p>
                    </div>
                    <div class="flex gap-3">
                        <span class="material-symbols-outlined text-[18px] text-text-muted">call</span>
                        <p class="font-medium">{{ $address->phone }}</p>
                    </div>
                </div>
                <div class="mt-6 flex gap-3">
                    <button type="button" wire:click="openEditForm({{ $address->id }})" class="flex-1 py-2 text-primary-600 font-bold text-xs border border-primary-200 dark:border-primary-800 rounded-lg hover:bg-primary-600 hover:text-white transition-all cursor-pointer text-center">Edit Details</button>
                    @if(!$isDefault)
                        <button type="button" wire:click="setAsDefault({{ $address->id }})" class="flex-1 py-2 text-text-secondary font-bold text-xs border border-border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-all cursor-pointer text-center">Set as Default</button>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Add Address Button Card -->
        @if(!$isFormOpen)
            <button type="button" wire:click="openCreateForm" class="flex flex-col items-center justify-center border-2 border-dashed border-border rounded-xl p-6 min-h-[220px] hover:border-primary-500 hover:bg-primary-50/10 dark:hover:bg-primary-950/10 transition-all group cursor-pointer text-center w-full">
                <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-text-muted group-hover:bg-primary-50 dark:group-hover:bg-primary-950/20 group-hover:text-primary-600 transition-all mb-4">
                    <span class="material-symbols-outlined text-[32px]">add_location_alt</span>
                </div>
                <span class="font-bold text-lg text-text-secondary group-hover:text-primary-600">Add a new delivery point</span>
                <p class="text-xs text-text-muted mt-2">Simplify your next checkout process</p>
            </button>
        @endif
    </div>

    <!-- Map Optimization Section -->
    <section class="mt-12">
        <h2 class="font-bold text-2xl text-text-primary mb-6">Nearby Delivery Points</h2>
        <div class="bg-bg-primary border border-border rounded-2xl overflow-hidden shadow-xs">
            <div class="relative h-80 w-full group">
                <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC_AmyNt149e8yYD6moVrbwP1L-nRv8yy21_fV3emHltsxMOU_UjIseZs4tdSoPXuAG-AxOQH1kiHdOBJsuuz3ibppOG7oGxfFaB-CXd7NhL7cXGRpOa01jA0Cp0Z4nvmWmKTEXeJ48uzxIObUBh0alzBUNEcxSa8zjD7ro9WKV7DkbmCwBosCGlfMfL7nTgm2Pm3V5qo5BTXdIDWMdZbXy_U9lBLz1V0vsjBg1G1qXzxgAsAelsLmRubEGfQCm2fVYM6NiBgBvDbM" alt="Location Map"/>
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent flex items-end p-6">
                    <div class="flex items-center gap-4 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md p-4 rounded-xl shadow-lg border border-white/20">
                        <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center text-white">
                            <span class="material-symbols-outlined">explore</span>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-primary-500 font-label-caps uppercase tracking-wider">Location Optimization</p>
                            <h4 class="text-text-primary font-bold text-sm">Closest Hub: Jakarta South</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-border">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary-500 mt-0.5">speed</span>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Priority Delivery</p>
                        <p class="text-xs text-text-secondary mt-1">Eligible for 2-hour shipping</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary-500 mt-0.5">verified</span>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Verified Addresses</p>
                        <p class="text-xs text-text-secondary mt-1">Postal-coded formatting verification</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary-500 mt-0.5">local_shipping</span>
                    <div>
                        <p class="font-bold text-text-primary text-sm">Courier Network</p>
                        <p class="text-xs text-text-secondary mt-1">Integrated with 4 local couriers</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
