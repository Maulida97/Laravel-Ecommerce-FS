<div>
    @section('title', 'My Orders — Tokoku.id')

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <h1 class="font-bold text-3xl text-text-primary mb-2">My Orders</h1>
            <p class="text-base text-text-secondary">Track, manage, and view the history of all your purchases.</p>
        </div>
        <div class="flex gap-4">
            <div class="bg-gray-50 dark:bg-gray-800 border border-border px-5 py-3 rounded-2xl flex flex-col items-center justify-center min-w-[100px] shadow-xs">
                <span class="font-bold text-2xl text-primary-600">{{ $activeCount }}</span>
                <span class="text-[10px] font-bold text-text-muted uppercase tracking-widest mt-1 font-label-caps">Active</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-800 border border-border px-5 py-3 rounded-2xl flex flex-col items-center justify-center min-w-[100px] shadow-xs">
                <span class="font-bold text-2xl text-text-secondary">{{ $completedCount }}</span>
                <span class="text-[10px] font-bold text-text-muted uppercase tracking-widest mt-1 font-label-caps">Completed</span>
            </div>
        </div>
    </div>

    <!-- Bento Filter Bar -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="md:col-span-2 relative">
            <span class="absolute inset-y-0 left-4 flex items-center text-text-muted">
                <span class="material-symbols-outlined">search</span>
            </span>
            <input type="text" wire:model.live.debounce.300ms="search" class="w-full pl-12 pr-4 py-3 bg-bg-primary border border-border rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all text-text-primary placeholder-text-muted" placeholder="Search by Order ID..."/>
        </div>
        <div class="flex gap-4">
            <select wire:model.live="statusFilter" class="w-full bg-bg-primary border border-border rounded-xl px-4 py-3 font-medium outline-none focus:ring-2 focus:ring-primary text-text-primary cursor-pointer">
                <option value="all">Status: All</option>
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
                <option value="returned">Returned</option>
            </select>
        </div>
        <button type="button" wire:click="$set('statusFilter', 'all')" class="bg-bg-primary border border-border rounded-xl px-4 py-3 flex items-center justify-center gap-2 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-text-secondary cursor-pointer">
            <span class="material-symbols-outlined text-[20px]">restart_alt</span>
            <span class="font-bold font-label-caps text-xs uppercase">Reset Filter</span>
        </button>
    </div>

    <!-- Orders Table Container -->
    <div class="bg-bg-primary border border-border rounded-2xl shadow-sm overflow-hidden mb-12">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50/50 dark:bg-gray-800/30">
                    <tr>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps">Order ID</th>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps">Date</th>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps">Status</th>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps">Items</th>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps">Total Amount</th>
                        <th class="px-6 py-4 font-semibold text-xs text-text-muted uppercase tracking-wider border-b border-border font-label-caps text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/40 dark:hover:bg-gray-800/10 transition-colors group">
                            <td class="px-6 py-5">
                                <span class="font-bold text-text-primary">#{{ $order->order_number }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-text-secondary text-sm">{{ $order->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-gray-100 dark:bg-gray-800 text-text-muted border-gray-200 dark:border-gray-700',
                                        'processing' => 'bg-primary-50 dark:bg-primary-950/20 text-primary-500 border-primary-200 dark:border-primary-800/40',
                                        'shipped' => 'bg-blue-50 dark:bg-blue-950/20 text-blue-500 border-blue-200 dark:border-blue-800/40',
                                        'delivered' => 'bg-emerald-50 dark:bg-emerald-950/20 text-success border-emerald-200 dark:border-emerald-800/40',
                                        'cancelled' => 'bg-red-50 dark:bg-red-950/20 text-danger border-red-200 dark:border-red-800/40',
                                        'returned' => 'bg-amber-50 dark:bg-amber-950/20 text-warning border-amber-200 dark:border-amber-800/40',
                                    ];
                                    $dotClasses = [
                                        'pending' => 'bg-text-muted',
                                        'processing' => 'bg-primary-500 animate-pulse',
                                        'shipped' => 'bg-blue-500',
                                        'delivered' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        'returned' => 'bg-warning',
                                    ];
                                    $status = $order->order_status;
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$status] ?? $statusClasses['pending'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-2 {{ $dotClasses[$status] ?? $dotClasses['pending'] }}"></span>
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex -space-x-2">
                                    @foreach($order->items->take(2) as $item)
                                        <div class="w-8 h-8 rounded-lg border-2 border-bg-primary overflow-hidden bg-gray-100 shadow-xs" title="{{ $item->product?->name ?? 'Product' }}">
                                            <img class="w-full h-full object-cover" src="{{ $item->product?->primary_image_url }}" alt="Item">
                                        </div>
                                    @endforeach
                                    @if($order->items->count() > 2)
                                        <div class="w-8 h-8 rounded-lg border-2 border-bg-primary overflow-hidden bg-gray-100 flex items-center justify-center text-[10px] font-bold text-text-secondary shadow-xs">
                                            +{{ $order->items->count() - 2 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5 font-bold text-text-primary">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-5 text-right">
                                <a href="{{ route('order.track', ['order_number' => $order->order_number]) }}" class="text-primary-600 hover:underline font-bold text-sm cursor-pointer">Track Order</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-text-secondary">
                                <div class="flex flex-col items-center gap-3">
                                    <div style="width: 64px; height: 64px; background: var(--primary-50); color: var(--primary-500); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center;">
                                        <span class="material-symbols-outlined text-3xl">shopping_cart</span>
                                    </div>
                                    <span class="font-medium text-base">No orders found matching the filter.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Footer -->
        @if ($orders->hasPages())
            <div class="px-6 py-4 bg-gray-50/30 dark:bg-gray-800/10 border-t border-border flex items-center justify-between">
                <span class="text-sm text-text-secondary">
                    Showing <strong>{{ $orders->firstItem() }}</strong> to <strong>{{ $orders->lastItem() }}</strong> of <strong>{{ $orders->total() }}</strong> orders
                </span>
                <div class="flex gap-1">
                    {{-- Previous Page Link --}}
                    @if ($orders->onFirstPage())
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-border opacity-50 cursor-not-allowed" disabled>
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                    @else
                        <button wire:click="previousPage" class="w-10 h-10 flex items-center justify-center rounded-lg border border-border bg-bg-primary text-text-primary hover:bg-gray-50 cursor-pointer">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                    @endif

                    {{-- Page Numbers --}}
                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <button class="w-10 h-10 flex items-center justify-center rounded-lg bg-primary-600 text-white font-bold">{{ $page }}</button>
                        @else
                            <button wire:click="gotoPage({{ $page }})" class="w-10 h-10 flex items-center justify-center rounded-lg border border-border bg-bg-primary text-text-primary hover:bg-gray-50 cursor-pointer">{{ $page }}</button>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($orders->hasMorePages())
                        <button wire:click="nextPage" class="w-10 h-10 flex items-center justify-center rounded-lg border border-border bg-bg-primary text-text-primary hover:bg-gray-50 cursor-pointer">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    @else
                        <button class="w-10 h-10 flex items-center justify-center rounded-lg border border-border opacity-50 cursor-not-allowed" disabled>
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <!-- Help Section (Asymmetric Bento Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 bg-gradient-to-r from-primary-600 to-indigo-700 p-8 rounded-3xl text-white relative overflow-hidden group shadow-sm">
            <div class="relative z-10 max-w-md" style="display: flex; flex-direction: column; align-items: flex-start; gap: var(--space-2);">
                <h2 class="font-bold text-2xl mb-1">Need help with an order?</h2>
                <p class="opacity-95 text-sm leading-relaxed mb-4">Our 24/7 support team is here to assist with tracking, returns, or any questions about your purchases.</p>
                <button type="button" class="bg-white text-primary-700 px-6 py-3 rounded-xl font-bold hover:scale-105 transition-transform active:scale-95 cursor-pointer shadow-sm">Contact Support</button>
            </div>
            <!-- Decorative element -->
            <div class="absolute -right-20 -bottom-20 w-64 h-64 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-500"></div>
        </div>
        <div class="bg-bg-primary border border-border p-8 rounded-3xl flex flex-col items-center text-center justify-center shadow-xs">
            <div class="w-16 h-16 rounded-full bg-primary-50 dark:bg-primary-950/20 text-primary-600 flex items-center justify-center mb-4 shadow-inner">
                <span class="material-symbols-outlined text-[32px]">assignment_return</span>
            </div>
            <h3 class="font-bold text-lg text-text-primary mb-1">Easy Returns</h3>
            <p class="text-sm text-text-secondary mb-4">Not satisfied? Return within 30 days.</p>
            <a class="text-primary-600 font-bold text-sm hover:underline cursor-pointer" href="#">Return Policy →</a>
        </div>
    </div>
</div>
