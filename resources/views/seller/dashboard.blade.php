@extends('layouts.seller')

@section('header_title', 'Dashboard Penjual')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Total Produk" value="{{ $total_products }}" icon="package" iconBgColor="bg-emerald-100"
                iconColor="text-emerald-700" />
            <x-stat-card title="Pesanan Tertunda" value="{{ $pending_orders }}" icon="shopping-cart"
                iconBgColor="bg-amber-100" iconColor="text-amber-700" />
            <x-stat-card title="Pesanan Selesai" value="{{ $completed_orders }}" icon="check-circle"
                iconBgColor="bg-green-100" iconColor="text-green-700" />
            <x-stat-card title="Pendapatan" value="{{ $revenue }}" icon="dollar-sign" iconBgColor="bg-emerald-100"
                iconColor="text-emerald-700" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Ikhtisar Pendapatan</h3>
                        <p class="text-sm text-gray-500">Tren pendapatan bulanan</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-green-600">
                        <i data-lucide="trending-up" class="w-4 h-4"></i>
                        <span class="font-medium">+{{ $growth }}% Pertumbuhan</span>
                    </div>
                </div>
                <div class="relative h-[280px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-[400px]">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-gray-900">Chat Terbaru</h3>
                    <a href="{{ route('seller.chats') }}" class="text-xs text-emerald-700 font-bold hover:underline">Lihat
                        Semua</a>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-2">
                    @forelse($recent_chats ?? [] as $chat)
                        <a href="{{ route('seller.chats') }}?user_id={{ $chat['user_id'] }}"
                            class="flex items-center gap-3 p-3 hover:bg-emerald-50 rounded-xl transition-all group border-b border-gray-50 last:border-0">
                            <div class="relative">
                                <div
                                    class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center overflow-hidden shrink-0 border-2 border-white shadow-sm">
                                    @if($chat['avatar'])
                                        <img src="{{ $chat['avatar'] }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-orange-600 font-bold text-sm">{{ substr($chat['name'], 0, 1) }}</span>
                                    @endif
                                </div>
                                @if(!$chat['is_read'])
                                    <span
                                        class="absolute top-0 right-0 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-center mb-0.5">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-emerald-800">
                                        {{ $chat['name'] }}</p>
                                    <span class="text-[10px] text-gray-400 shrink-0">{{ $chat['time'] }}</span>
                                </div>
                                <p class="text-xs text-gray-500 truncate group-hover:text-amber-700">{{ $chat['last_message'] }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-12">
                            <i data-lucide="message-square" class="w-12 h-12 text-gray-200 mx-auto mb-4"></i>
                            <p class="text-sm text-gray-500">Tidak ada pesan terbaru</p>
                            <a href="{{ route('seller.chats') }}"
                                class="mt-4 inline-block text-xs text-emerald-700 font-bold">Buka Menu Chat</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Pesanan Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ID Pesanan</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pelanggan</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Produk</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah</th>
                                <th class="text-left py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_orders as $order)
                                <tr class="border-b border-gray-50 hover:bg-gray-50 transition-all">
                                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $order['id'] }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-700">{{ $order['customer'] }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-700">{{ $order['product'] }}</td>
                                    <td class="py-4 px-4 text-sm font-medium text-gray-900">{{ $order['amount'] }}</td>
                                    <td class="py-4 px-4">
                                        <x-badge variant="{{ $order['status'] === 'completed' ? 'active' : 'pending' }}">
                                            {{ $order['status'] }}
                                        </x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-500 text-sm">
                                        <span class="italic">Belum ada pesanan</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex flex-col h-[400px]">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Produk Unggulan</h3>
                </div>
                <div class="space-y-4 flex-1 overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($top_products ?? [] as $product)
                        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-all">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                                    @if($product['image'] ?? null)
                                        <img src="{{ $product['image'] }}" class="w-full h-full object-cover">
                                    @else
                                        <i data-lucide="package" class="w-5 h-5 text-emerald-700"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $product['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $product['sales'] }} Terjual</p>
                                </div>
                            </div>
                            <p class="text-sm font-bold text-amber-600">{{ $product['price'] }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4 italic">Belum ada data produk...</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chart['labels'] ?? []) !!},
                    datasets: [{
                        label: 'Pendapatan',
                        data: {!! json_encode($chart['data'] ?? []) !!},
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
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
                    const currentStats = document.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4');
                    const newStats = doc.querySelector('.grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4');
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