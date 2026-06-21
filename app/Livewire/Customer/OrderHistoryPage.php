<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderHistoryPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';

    protected $paginationTheme = 'tailwind';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when search or filters change.
     */
    public function updating($property)
    {
        if (in_array($property, ['search', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        if (auth()->user()->isAdmin()) {
            return $this->redirect(route('admin.dashboard', absolute: false), navigate: true);
        }

        $user = auth()->user();

        // Calculate summary counts
        $activeCount = Order::where('user_id', $user->id)
            ->whereIn('order_status', ['pending', 'processing', 'shipped'])
            ->count();

        $completedCount = Order::where('user_id', $user->id)
            ->where('order_status', 'delivered')
            ->count();

        $query = Order::where('user_id', $user->id)
            ->with(['items.product.primaryImage']);

        if (!empty($this->search)) {
            $query->where('order_number', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('order_status', $this->statusFilter);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.customer.order-history-page', [
            'orders' => $orders,
            'activeCount' => $activeCount,
            'completedCount' => $completedCount,
        ])->layout('layouts.customer-portal');
    }
}
