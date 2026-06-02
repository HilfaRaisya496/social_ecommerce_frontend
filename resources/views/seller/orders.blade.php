@extends('layouts.seller')

@section('header_title', 'Manajemen Pesanan')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-1">Manajemen Pesanan</h2>
                <p class="text-sm text-gray-500">Lihat dan kelola pesanan pelanggan Anda</p>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input type="text" id="orderSearch" placeholder="Cari nomor pesanan, pelanggan, atau nama produk..."
                        value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                </div>
            </div>

            <div id="ordersTableContainer">
                <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full relative">
                        <thead class="sticky top-0 z-10 bg-white shadow-sm">
                            <tr class="border-b border-gray-100">
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">ID Pesanan</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Pelanggan</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Produk</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Jumlah</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Total Harga</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Status</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Tanggal</th>
                                <th class="text-left py-3 px-4 text-sm font-medium text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50/50 transition-all">
                                    <td class="py-4 px-4 text-sm font-bold text-gray-900">{{ $order['order_number'] }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-700 font-medium">{{ $order['buyer_name'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="space-y-1">
                                            @foreach($order['items'] as $item)
                                                <div class="text-xs text-gray-600 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                                    <span class="line-clamp-1">{{ $item['name'] }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-sm text-gray-600 font-medium text-center">
                                        {{ $order['products_count'] }}
                                    </td>
                                    <td class="py-4 px-4 text-sm font-bold text-emerald-700">{{ $order['total_price'] }}</td>
                                    <td class="py-4 px-4">
                                        @php
                                            $statusConfig = [
                                                'pending' => ['label' => 'Menunggu', 'class' => 'bg-amber-50 text-amber-600 border-amber-100'],
                                                'processed' => ['label' => 'Diproses', 'class' => 'bg-blue-50 text-blue-600 border-blue-100'],
                                                'shipped' => ['label' => 'Dikirim', 'class' => 'bg-purple-50 text-purple-600 border-purple-100'],
                                                'completed' => ['label' => 'Selesai', 'class' => 'bg-green-50 text-green-600 border-green-100'],
                                            ];
                                            $config = $statusConfig[$order['status']] ?? ['label' => $order['status'], 'class' => 'bg-gray-50 text-gray-500 border-gray-100'];
                                        @endphp
                                        <span
                                            class="px-2.5 py-1 rounded-lg border {{ $config['class'] }} text-[10px] font-bold uppercase tracking-wider">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-[11px] text-gray-500 whitespace-nowrap">{{ $order['date'] }}</td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('seller.orders.show', $order['id']) }}"
                                                class="p-2 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all"
                                                title="Lihat Detail">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                                <i data-lucide="shopping-bag" class="w-8 h-8 text-gray-200"></i>
                                            </div>
                                            <h3 class="text-sm font-bold text-gray-900">Belum ada pesanan</h3>
                                            <p class="text-xs text-gray-500 mt-1">Pesanan dari pelanggan akan muncul di sini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($orders->hasPages())
                    <div class="mt-6 pt-6 border-t border-gray-50 flex items-center justify-center">
                        <div class="flex items-center gap-2">
                            @if($orders->onFirstPage())
                                <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevron-left"
                                        class="w-5 h-5"></i></span>
                            @else
                                <a href="{{ $orders->previousPageUrl() }}"
                                    class="p-2 text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all"><i
                                        data-lucide="chevron-left" class="w-5 h-5"></i></a>
                            @endif

                            <div class="flex items-center gap-1">
                                @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                    @if ($page == $orders->currentPage())
                                        <span
                                            class="w-9 h-9 flex items-center justify-center bg-emerald-700 text-white rounded-lg text-xs font-bold">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}"
                                            class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-50 rounded-lg text-xs font-medium transition-all">{{ $page }}</a>
                                    @endif
                                @endforeach
                            </div>

                            @if($orders->hasMorePages())
                                <a href="{{ $orders->nextPageUrl() }}"
                                    class="p-2 text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-all"><i
                                        data-lucide="chevron-right" class="w-5 h-5"></i></a>
                            @else
                                <span class="p-2 text-gray-300 cursor-not-allowed"><i data-lucide="chevron-right"
                                        class="w-5 h-5"></i></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('orderSearch');
            const tableContainer = document.getElementById('ordersTableContainer');
            let timeout = null;

            if (searchInput && tableContainer) {
                searchInput.addEventListener('input', function () {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        const searchValue = this.value;

                        // Show loading state
                        tableContainer.classList.add('opacity-50');

                        fetch(`{{ route('seller.orders') }}?search=${encodeURIComponent(searchValue)}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                            .then(response => response.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContent = doc.getElementById('ordersTableContainer');

                                if (newContent) {
                                    tableContainer.innerHTML = newContent.innerHTML;

                                    // Re-init lucide icons
                                    if (window.lucide) {
                                        window.lucide.createIcons();
                                    }
                                }

                                tableContainer.classList.remove('opacity-50');

                                // Update URL without reload
                                const newUrl = searchValue
                                    ? `{{ route('seller.orders') }}?search=${encodeURIComponent(searchValue)}`
                                    : `{{ route('seller.orders') }}`;
                                window.history.pushState({ path: newUrl }, '', newUrl);
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                tableContainer.classList.remove('opacity-50');
                            });
                    }, 500); // 500ms debounce
                });
            }

            // Handle back/forward buttons
            window.addEventListener('popstate', function () {
                location.reload();
            });
        });
    </script>
@endsection