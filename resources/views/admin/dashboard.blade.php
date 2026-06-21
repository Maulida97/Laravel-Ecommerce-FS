@extends('layouts.admin')

@section('title', 'Admin Dashboard — Tokoku.id')

@section('menu-dashboard-active', 'active')

@section('breadcrumb')
    <span>Dashboard</span> / <span>Overview</span>
@endsection

@section('styles')
    @vite(['resources/css/dashboard.css'])
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: var(--primary-50); color: var(--primary-500);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <span class="stat-trend trend-up">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    New
                </span>
            </div>
            <div class="stat-value" data-count="{{ $totalOrders }}">0</div>
            <div class="stat-label">Total Orders</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: #ecfdf5; color: var(--success);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <span class="stat-trend trend-up">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Real
                </span>
            </div>
            <div class="stat-value" data-count="{{ (int)$totalSales }}">0</div>
            <div class="stat-label">Total Sales (Rp)</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: #fff7ed; color: var(--warning);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="stat-trend trend-up">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    Active
                </span>
            </div>
            <div class="stat-value" data-count="{{ $activeUsers }}">0</div>
            <div class="stat-label">Active Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: #fef2f2; color: var(--danger);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
                <span class="stat-trend trend-down">
                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    Items
                </span>
            </div>
            <div class="stat-value" data-count="{{ $totalProducts }}">0</div>
            <div class="stat-label">Products</div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="dashboard-grid">
        <!-- Sales Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Sales Overview</h3>
                <div style="display: flex; gap: var(--space-2);">
                    <button class="page-btn active" data-range="7d">7 Days</button>
                    <button class="page-btn" data-range="30d">30 Days</button>
                    <button class="page-btn" data-range="90d">90 Days</button>
                </div>
            </div>
            <div class="chart-container" id="salesChart">
                <svg class="chart-svg" id="salesSvg"></svg>
                <div class="chart-tooltip" id="chartTooltip"></div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
                <a href="#" class="card-link">View All →</a>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="ordersTable">
                    @forelse($recentOrders as $order)
                        <tr>
                            <td><strong>#{{ $order->order_number }}</strong></td>
                            <td>{{ $order->guest_name ?? ($order->user ? $order->user->name : 'Guest') }}</td>
                            <td>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="status-badge status-{{ $order->payment_status }}">
                                    <span class="status-dot" style="background: {{ $order->payment_status === 'paid' ? 'var(--success)' : ($order->payment_status === 'pending' ? 'var(--warning)' : 'var(--danger)') }};"></span>
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: var(--space-4);">No recent orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination">
                <button class="page-btn">←</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">→</button>
            </div>
        </div>
    </div>

    <!-- Bottom Grid -->
    <div class="dashboard-grid-2">
        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Top Products</h3>
            </div>
            <div id="topProducts">
                @forelse($topProducts as $idx => $product)
                    <div class="bar-chart-item">
                        <div class="bar-chart-header">
                            <span>{{ $product->name }}</span>
                            <strong>{{ number_format($product->sold_count) }} sold</strong>
                        </div>
                        <div class="bar-chart-track">
                            <div class="bar-chart-fill" style="width: {{ max(10, min(100, (100 - $idx * 15))) }}%;"></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: var(--space-4); color: var(--text-muted);">No top products yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Low Stock Alerts</h3>
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: var(--danger);"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div id="lowStock">
                @forelse($lowStockProducts as $product)
                    <div class="stock-item">
                        <img src="{{ $product->primary_image_url }}" alt="" class="stock-img">
                        <div class="stock-info">
                            <div class="stock-name">{{ $product->name }}</div>
                        </div>
                        <span class="stock-count {{ $product->stock_quantity <= 2 ? 'low' : 'warn' }}">{{ $product->stock_quantity }} left</span>
                        <button class="btn-restock">Restock</button>
                    </div>
                @empty
                    <div style="text-align: center; padding: var(--space-4); color: var(--text-muted); width: 100%;">All items have healthy stock.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    (function() {
        'use strict';

        // Number Count Animation
        function animateNumber(element, target, duration = 1500) {
            const start = performance.now();
            const startValue = 0;

            function update(currentTime) {
                const elapsed = currentTime - start;
                const progress = Math.min(elapsed / duration, 1);
                const easeProgress = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(startValue + (target - startValue) * easeProgress);

                element.textContent = current.toLocaleString();

                if (progress < 1) {
                    requestAnimationFrame(update);
                }
            }

            requestAnimationFrame(update);
        }

        // Intersection Observer for count animation
        const statValues = document.querySelectorAll('.stat-value[data-count]');
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.count);
                    animateNumber(el, target);
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        statValues.forEach(el => countObserver.observe(el));

        // SVG Line Chart Logic
        const chartData = {
            '7d': @json($sales7d),
            '30d': @json($sales30d),
            '90d': @json($sales90d)
        };

        const chartLabels = {
            '7d': @json($labels7d),
            '30d': @json($labels30d),
            '90d': @json($labels90d)
        };

        function drawLineChart(range) {
            const svg = document.getElementById('salesSvg');
            const tooltip = document.getElementById('chartTooltip');
            if (!svg || !tooltip) return;

            const data = chartData[range];
            const labels = chartLabels[range];
            const width = svg.clientWidth || 600;
            const height = 280;
            const padding = { top: 20, right: 20, bottom: 40, left: 50 };
            const chartWidth = width - padding.left - padding.right;
            const chartHeight = height - padding.top - padding.bottom;

            const maxValue = Math.max(...data) * 1.1;
            const minValue = 0;

            svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
            svg.innerHTML = '';

            // Grid lines
            const gridCount = 5;
            for (let i = 0; i <= gridCount; i++) {
                const y = padding.top + (chartHeight / gridCount) * i;
                const value = Math.round(maxValue - (maxValue / gridCount) * i);

                // Grid line
                const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line.setAttribute('x1', padding.left);
                line.setAttribute('y1', y);
                line.setAttribute('x2', width - padding.right);
                line.setAttribute('y2', y);
                line.setAttribute('stroke', 'var(--gray-200)');
                line.setAttribute('stroke-dasharray', '4,4');
                svg.appendChild(line);

                // Y label
                const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                text.setAttribute('x', padding.left - 10);
                text.setAttribute('y', y + 4);
                text.setAttribute('text-anchor', 'end');
                text.setAttribute('fill', 'var(--text-muted)');
                text.setAttribute('font-size', '11');
                text.textContent = `Rp ${(value/1000000).toFixed(1)}jt`;
                svg.appendChild(text);
            }

            // Generate path points
            const points = data.map((value, i) => ({
                x: padding.left + (chartWidth / (data.length - 1)) * i,
                y: padding.top + chartHeight - ((value - minValue) / (maxValue - minValue)) * chartHeight,
                value,
                label: labels[i]
            }));

            // Smooth curve path
            let pathD = `M ${points[0].x} ${points[0].y}`;
            for (let i = 0; i < points.length - 1; i++) {
                const curr = points[i];
                const next = points[i + 1];
                const cp1x = curr.x + (next.x - curr.x) * 0.3;
                const cp1y = curr.y;
                const cp2x = next.x - (next.x - curr.x) * 0.3;
                const cp2y = next.y;
                pathD += ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${next.x} ${next.y}`;
            }

            // Gradient fill setup
            const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
            const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
            gradient.setAttribute('id', 'chartGradient');
            gradient.setAttribute('x1', '0');
            gradient.setAttribute('y1', '0');
            gradient.setAttribute('x2', '0');
            gradient.setAttribute('y2', '1');

            const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stop1.setAttribute('offset', '0%');
            stop1.setAttribute('stop-color', 'var(--primary-500)');
            stop1.setAttribute('stop-opacity', '0.2');

            const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
            stop2.setAttribute('offset', '100%');
            stop2.setAttribute('stop-color', 'var(--primary-500)');
            stop2.setAttribute('stop-opacity', '0');

            gradient.appendChild(stop1);
            gradient.appendChild(stop2);
            defs.appendChild(gradient);
            svg.appendChild(defs);

            // Fill area
            const fillPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            fillPath.setAttribute('d', `${pathD} L ${points[points.length-1].x} ${height - padding.bottom} L ${points[0].x} ${height - padding.bottom} Z`);
            fillPath.setAttribute('fill', 'url(#chartGradient)');
            svg.appendChild(fillPath);

            // Line stroke
            const linePath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            linePath.setAttribute('d', pathD);
            linePath.setAttribute('fill', 'none');
            linePath.setAttribute('stroke', 'var(--primary-500)');
            linePath.setAttribute('stroke-width', '2.5');
            linePath.setAttribute('stroke-linecap', 'round');
            svg.appendChild(linePath);

            // Interaction points and X labels
            points.forEach((point, i) => {
                const showLabel = range === '7d' || (range === '30d' && i % 5 === 0) || (range === '90d' && i % 10 === 0);

                if (showLabel) {
                    const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    text.setAttribute('x', point.x);
                    text.setAttribute('y', height - padding.bottom + 20);
                    text.setAttribute('text-anchor', 'middle');
                    text.setAttribute('fill', 'var(--text-muted)');
                    text.setAttribute('font-size', '11');
                    text.textContent = point.label;
                    svg.appendChild(text);
                }

                // Hover interaction rect
                const hoverArea = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                hoverArea.setAttribute('x', point.x - (chartWidth / data.length / 2));
                hoverArea.setAttribute('y', padding.top);
                hoverArea.setAttribute('width', chartWidth / data.length);
                hoverArea.setAttribute('height', chartHeight);
                hoverArea.setAttribute('fill', 'transparent');
                hoverArea.style.cursor = 'pointer';

                hoverArea.addEventListener('mouseenter', () => {
                    // Active point dot
                    const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    circle.setAttribute('cx', point.x);
                    circle.setAttribute('cy', point.y);
                    circle.setAttribute('r', '5');
                    circle.setAttribute('fill', 'white');
                    circle.setAttribute('stroke', 'var(--primary-500)');
                    circle.setAttribute('stroke-width', '2.5');
                    circle.setAttribute('id', 'activePoint');
                    svg.appendChild(circle);

                    // Show tooltip
                    tooltip.innerHTML = `<strong>Rp ${point.value.toLocaleString('id-ID')}</strong><br><span style="opacity:0.7">${point.label}</span>`;
                    tooltip.classList.add('visible');
                    tooltip.style.left = `${point.x - tooltip.offsetWidth / 2}px`;
                    tooltip.style.top = `${point.y - tooltip.offsetHeight - 10}px`;
                });

                hoverArea.addEventListener('mouseleave', () => {
                    const activePoint = document.getElementById('activePoint');
                    if (activePoint) activePoint.remove();
                    tooltip.classList.remove('visible');
                });

                svg.appendChild(hoverArea);
            });
        }

        // Chart Range Toggle Buttons
        document.querySelectorAll('[data-range]').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('[data-range]').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                drawLineChart(btn.dataset.range);
            });
        });

        // Initialize Line Chart on load and resize
        drawLineChart('7d');
        window.addEventListener('resize', () => {
            const activeRangeBtn = document.querySelector('[data-range].active');
            if (activeRangeBtn) {
                drawLineChart(activeRangeBtn.dataset.range);
            }
        });

        // Restock button interaction
        document.querySelectorAll('.btn-restock').forEach(btn => {
            btn.addEventListener('click', function() {
                const original = this.textContent;
                this.textContent = 'Done';
                this.style.background = 'var(--success)';
                this.style.color = 'white';
                setTimeout(() => {
                    this.textContent = original;
                    this.style.background = '';
                    this.style.color = '';
                }, 1500);
            });
        });

    })();
</script>
@endsection
