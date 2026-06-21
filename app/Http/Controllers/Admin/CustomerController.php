<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    /**
     * Display a listing of the customers.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('orders')
            ->withSum(['orders as total_spent' => function ($q) {
                $q->where('payment_status', 'paid');
            }], 'total_amount');

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sort = $request->input('sort', 'created_at');
        $direction = $request->input('direction', 'desc');

        if ($sort === 'orders_count') {
            $query->orderBy('orders_count', $direction);
        } elseif ($sort === 'total_spent') {
            // Note: withSum creates orders_sum_total_amount or total_spent depending on the alias.
            // In our query, the alias is `total_spent`
            $query->orderBy('total_spent', $direction);
        } else {
            $query->orderBy($sort, $direction);
        }

        $customers = $query->paginate(15)->withQueryString();

        return view('admin.customers.index', compact('customers', 'sort', 'direction'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        // Not used as we do CRUD in modal, but here for completeness
        return redirect()->route('admin.customers.index');
    }

    /**
     * Store a newly created customer in database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $customer = new User();
        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        $customer->password = $validated['password']; // hashed automatically by User model casts
        $customer->phone = $validated['phone'];
        $customer->address = $validated['address'];
        $customer->bio = $validated['bio'];
        $customer->role = 'customer';

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $customer->avatar = Storage::disk('public')->url($path);
        }

        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer's statistics and shopping logs.
     */
    public function show(User $customer): View
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        // 1. Core stats
        $totalOrders = Order::where('user_id', $customer->id)->count();
        $totalSpent = (float) Order::where('user_id', $customer->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? $totalSpent / $totalOrders : 0;

        // 2. Order status distribution
        $statusCounts = Order::where('user_id', $customer->id)
            ->selectRaw('order_status, count(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        // 3. Purchase items log ("Belanja apa saja")
        $purchasedItems = OrderItem::whereHas('order', function ($q) use ($customer) {
            $q->where('user_id', $customer->id);
        })
        ->with(['order', 'product'])
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'items_page')
        ->withQueryString();

        // 4. Order history
        $orders = Order::where('user_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'orders_page')
            ->withQueryString();

        return view('admin.customers.show', compact(
            'customer',
            'totalOrders',
            'totalSpent',
            'averageOrderValue',
            'statusCounts',
            'purchasedItems',
            'orders'
        ));
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(User $customer)
    {
        // Not used as we do CRUD in modal, but here for completeness
        return redirect()->route('admin.customers.index');
    }

    /**
     * Update the specified customer in database.
     */
    public function update(Request $request, User $customer): RedirectResponse
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $customer->name = $validated['name'];
        $customer->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $customer->password = $validated['password']; // hashed automatically by User model casts
        }

        $customer->phone = $validated['phone'];
        $customer->address = $validated['address'];
        $customer->bio = $validated['bio'];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($customer->avatar) {
                $filename = basename($customer->avatar);
                if (Storage::disk('public')->exists('avatars/' . $filename)) {
                    Storage::disk('public')->delete('avatars/' . $filename);
                }
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $customer->avatar = Storage::disk('public')->url($path);
        }

        $customer->save();

        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from database.
     */
    public function destroy(User $customer): RedirectResponse
    {
        if ($customer->role !== 'customer') {
            abort(404);
        }

        // Prevent delete if they have order history to maintain sales stats & DB integrity
        if (Order::where('user_id', $customer->id)->count() > 0) {
            return redirect()->route('admin.customers.index')->with('error', 'Cannot delete customer who has placed orders. You can edit their info or deactivate their account.');
        }

        // Delete avatar if exists
        if ($customer->avatar) {
            $filename = basename($customer->avatar);
            if (Storage::disk('public')->exists('avatars/' . $filename)) {
                Storage::disk('public')->delete('avatars/' . $filename);
            }
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
