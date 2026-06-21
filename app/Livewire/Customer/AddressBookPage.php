<?php

namespace App\Livewire\Customer;

use App\Models\Address;
use Livewire\Component;

class AddressBookPage extends Component
{
    public $addresses = [];
    
    // Form fields
    public $addressId = null;
    public $label = 'Home'; // e.g. Home, Office, Parents' House
    public $recipient_name = '';
    public $phone = '';
    public $address_line = '';
    public $city = '';
    public $postal_code = '';
    public $is_default = false;

    public bool $isFormOpen = false;
    public string $successMessage = '';

    protected $rules = [
        'label' => 'required|string|max:50',
        'recipient_name' => 'required|string|max:255',
        'phone' => 'required|string|max:20',
        'address_line' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'postal_code' => 'required|string|max:10',
        'is_default' => 'boolean',
    ];

    public function mount()
    {
        $this->loadAddresses();
    }

    /**
     * Load current user's addresses.
     */
    public function loadAddresses()
    {
        $this->addresses = auth()->user()->addresses()->orderBy('is_default', 'desc')->get();
    }

    /**
     * Open form to create a new address.
     */
    public function openCreateForm()
    {
        $this->resetForm();
        $this->isFormOpen = true;
        $this->successMessage = '';
    }

    /**
     * Open form to edit an existing address.
     */
    public function openEditForm($id)
    {
        $address = auth()->user()->addresses()->find($id);
        if (!$address) return;

        $this->addressId = $address->id;
        $this->label = $address->label;
        $this->recipient_name = $address->recipient_name;
        $this->phone = $address->phone;
        $this->address_line = $address->address_line;
        $this->city = $address->city;
        $this->postal_code = $address->postal_code;
        $this->is_default = $address->is_default;

        $this->isFormOpen = true;
        $this->successMessage = '';
    }

    /**
     * Reset form fields.
     */
    public function resetForm()
    {
        $this->addressId = null;
        $this->label = 'Home';
        $this->recipient_name = '';
        $this->phone = '';
        $this->address_line = '';
        $this->city = '';
        $this->postal_code = '';
        $this->is_default = false;
    }

    /**
     * Close the address edit/create form.
     */
    public function closeForm()
    {
        $this->isFormOpen = false;
        $this->resetForm();
    }

    /**
     * Create or update the address in database.
     */
    public function saveAddress()
    {
        $this->validate();

        $user = auth()->user();

        // If this address is set to default, unset other defaults first
        if ($this->is_default) {
            $user->addresses()->update(['is_default' => false]);
        }

        if ($this->addressId) {
            // Edit existing
            $address = $user->addresses()->find($this->addressId);
            if ($address) {
                $address->update([
                    'label' => $this->label,
                    'recipient_name' => $this->recipient_name,
                    'phone' => $this->phone,
                    'address_line' => $this->address_line,
                    'city' => $this->city,
                    'postal_code' => $this->postal_code,
                    'is_default' => $this->is_default,
                ]);
                $this->successMessage = 'Address updated successfully!';
            }
        } else {
            // Create new
            // If it's the user's first address, make it default automatically
            $isFirst = $user->addresses()->count() === 0;
            $user->addresses()->create([
                'label' => $this->label,
                'recipient_name' => $this->recipient_name,
                'phone' => $this->phone,
                'address_line' => $this->address_line,
                'city' => $this->city,
                'postal_code' => $this->postal_code,
                'is_default' => $this->is_default || $isFirst,
            ]);
            $this->successMessage = 'Address added successfully!';
        }

        $this->closeForm();
        $this->loadAddresses();
    }

    /**
     * Delete an address.
     */
    public function deleteAddress($id)
    {
        $address = auth()->user()->addresses()->find($id);
        if ($address) {
            $wasDefault = $address->is_default;
            $address->delete();

            // If we deleted the default, set another address as default if available
            if ($wasDefault) {
                $next = auth()->user()->addresses()->first();
                if ($next) {
                    $next->update(['is_default' => true]);
                }
            }

            $this->successMessage = 'Address deleted successfully!';
        }
        $this->loadAddresses();
    }

    /**
     * Mark an address as default.
     */
    public function setAsDefault($id)
    {
        $user = auth()->user();
        $user->addresses()->update(['is_default' => false]);
        
        $address = $user->addresses()->find($id);
        if ($address) {
            $address->update(['is_default' => true]);
            $this->successMessage = 'Default address updated!';
        }

        $this->loadAddresses();
    }

    public function render()
    {
        if (auth()->user()->isAdmin()) {
            return $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        }

        return view('livewire.customer.address-book-page')
            ->layout('layouts.customer-portal');
    }
}
