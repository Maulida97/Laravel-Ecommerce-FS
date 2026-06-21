<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::with('user')->latest();

        // 1. Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Order Status filter
        if ($request->filled('order_status')) {
            $query->where('order_status', $request->input('order_status'));
        }

        // 3. Payment Status filter
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // 4. Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->input('date_from')));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->input('date_to')));
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'statusHistories.user'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the order status.
     */
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled,returned',
            'tracking_number' => 'required_if:order_status,shipped|nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ], [
            'tracking_number.required_if' => 'Nomor resi wajib diisi jika status diubah menjadi dikirim (Shipped).',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $order->order_status;
            $newStatus = $validated['order_status'];

            $updateData = ['order_status' => $newStatus];

            if ($newStatus === 'shipped' && $request->filled('tracking_number')) {
                $updateData['tracking_number'] = $validated['tracking_number'];
            }

            $order->update($updateData);

            // Log status history
            $statusNotes = $validated['notes'];
            if (empty($statusNotes)) {
                $statusNotes = "Status pesanan diubah dari " . ucfirst($oldStatus) . " menjadi " . ucfirst($newStatus) . ".";
                if ($newStatus === 'shipped' && !empty($order->tracking_number)) {
                    $statusNotes .= " Nomor Resi: " . $order->tracking_number;
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'notes' => $statusNotes,
                'changed_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui status pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Update only the tracking number.
     */
    public function updateTracking(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $order->update([
                'tracking_number' => $validated['tracking_number']
            ]);

            $historyNotes = $validated['notes'] ?: "Nomor resi pengiriman diperbarui menjadi " . $validated['tracking_number'] . ".";

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->order_status,
                'notes' => $historyNotes,
                'changed_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Nomor resi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Gagal memperbarui nomor resi: ' . $e->getMessage());
        }
    }

    /**
     * Display print-friendly invoice.
     */
    public function invoice($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);

        return view('admin.orders.invoice', compact('order'));
    }
}
