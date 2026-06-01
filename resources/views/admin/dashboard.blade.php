@extends('layouts.admin')

@section('header_title', 'Ringkasan Dashboard')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-stat-card title="Total User" value="{{ number_format($total_users) }}" icon="users" iconBgColor="bg-gray-100"
                iconColor="text-gray-600" href="{{ route('admin.users') }}" />
            <x-stat-card title="Total Pembeli" value="{{ number_format($total_buyers ?? 0) }}" icon="user-check"
                iconBgColor="bg-emerald-100" iconColor="text-emerald-700" href="{{ route('admin.buyers') }}" />
            <x-stat-card title="Total Penjual" value="{{ number_format($total_sellers) }}" icon="store"
                iconBgColor="bg-amber-100" iconColor="text-amber-700" href="{{ route('admin.sellers') }}" />
            <x-stat-card title="Total Produk" value="{{ number_format($total_products) }}" icon="package"
                iconBgColor="bg-emerald-100" iconColor="text-emerald-700" href="{{ route('admin.products') }}" />
            <x-stat-card title="Total Transaksi" value="{{ number_format($total_transactions) }}" icon="receipt"
                iconBgColor="bg-amber-100" iconColor="text-amber-700" href="{{ route('admin.transactions') }}" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Statistik Pendaftaran</h3>
                        <p class="text-sm text-gray-500">Tren akun baru setiap bulan</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm {{ $growth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        <i data-lucide="{{ $growth >= 0 ? 'trending-up' : 'trending-down' }}" class="w-4 h-4"></i>
                        <span class="font-medium">{{ $growth >= 0 ? '+' : '' }}{{ $growth }}% Pertumbuhan</span>
                    </div>
                </div>
                <div class="relative h-[280px]">
                    <!-- Placeholder for Chart -->
                    <canvas id="growthChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Penjual Terbaik</h3>
                <div class="space-y-4">
                    @forelse($top_sellers ?? [] as $seller)
                        <a href="{{ route('admin.users.show', $seller['id']) }}"
                            class="flex items-center gap-4 p-2 hover:bg-gray-50 rounded-xl transition-all group">
                            <div
                                class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center overflow-hidden border border-amber-200 shadow-sm">
                                @if($seller['avatar'] ?? null)
                                    <img src="{{ Str::startsWith($seller['avatar'], 'http') ? $seller['avatar'] : env('BACKEND_URL') . '/storage/' . $seller['avatar'] }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <span class="text-amber-700 font-bold text-sm">{{ substr($seller['name'], 0, 1) }}</span>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-amber-700 transition-colors">
                                    {{ $seller['name'] }}
                                </h4>
                                <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">
                                    {{ $seller['sales'] }} Penjualan
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-gray-900">{{ $seller['revenue'] }}</p>
                            </div>
                            <div
                                class="text-gray-300 opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12">
                            <i data-lucide="award" class="w-12 h-12 text-gray-200 mx-auto mb-4"></i>
                            <p class="text-sm text-gray-500">No seller data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Transaksi Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                            </th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Penjual</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Pembeli</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jumlah</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_transactions ?? [] as $txn)
                            <tr class="border-b border-gray-50 hover:bg-amber-50 transition-all cursor-pointer group"
                                onclick="window.location='{{ route('admin.transactions.show', $txn['raw_id']) }}'">
                                <td
                                    class="py-4 px-4 text-sm font-bold text-gray-900 group-hover:text-amber-700 transition-colors">
                                    {{ $txn['id'] }}
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700 font-medium">{{ $txn['seller'] }}</td>
                                <td class="py-4 px-4 text-sm text-gray-700">{{ $txn['buyer'] }}</td>
                                <td class="py-4 px-4 text-sm font-bold text-gray-900">{{ $txn['amount'] }}</td>
                                <td class="py-4 px-4 flex items-center justify-between">
                                    <x-badge
                                        variant="{{ $txn['status'] === 'completed' ? 'active' : ($txn['status'] === 'cancelled' ? 'failed' : 'pending') }}">
                                        {{ $txn['status'] }}
                                    </x-badge>
                                    <i data-lucide="arrow-right"
                                        class="w-4 h-4 text-gray-300 opacity-0 group-hover:opacity-100 transition-all -translate-x-2 group-hover:translate-x-0"></i>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 text-sm">
                                    <div class="flex flex-col items-center justify-center italic">
                                        <span>No transactions found.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const chartData = @json($chart_data ?? ['labels' => [], 'users' => [], 'sellers' => []]);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.labels,
                    datasets: [{
                        label: 'Pembeli',
                        data: chartData.users,
                        borderColor: '#059669', /* Emerald 600 */
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Penjual',
                        data: chartData.sellers,
                        borderColor: '#d97706', /* Amber 600 */
                        backgroundColor: 'rgba(217, 119, 6, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' },
                            ticks: { stepSize: 1 }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Silent Auto-Refresh every 30 seconds
            setInterval(async () => {
                try {
                    const response = await fetch(window.location.href);
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Update stat cards
                    const currentStats = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5');
                    const newStats = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-5');
                    if (currentStats && newStats) {
                        currentStats.innerHTML = newStats.innerHTML;
                    }

                    // Update recent transactions
                    const currentTable = document.querySelector('table tbody');
                    const newTable = doc.querySelector('table tbody');
                    if (currentTable && newTable) {
                        currentTable.innerHTML = newTable.innerHTML;
                    }
                } catch (error) {
                    console.error('Silently failed to auto-refresh data:', error);
                }
            }, 30000);
        });
    </script>
@endsection