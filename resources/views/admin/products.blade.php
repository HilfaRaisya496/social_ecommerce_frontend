@extends('layouts.admin')

@section('header_title', 'Monitoring Produk')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-stat-card title="Total Produk" value="{{ $stats['total_products'] ?? 0 }}" icon="package"
                iconBgColor="bg-emerald-50" iconColor="text-emerald-600" />
            <x-stat-card title="Habis (Out of Stock)" value="{{ $stats['out_of_stock_products'] ?? 0 }}"
                icon="alert-octagon" iconBgColor="bg-red-100" iconColor="text-red-600" />
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-gray-900 mb-1">Manajemen Produk</h2>
                <p class="text-sm text-gray-500">Monitor seluruh produk yang ada di marketplace</p>
            </div>

            <div class="flex flex-col md:flex-row gap-4 mb-6">
                <div class="flex-1 relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari produk atau penjual..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="productTable">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Penjual</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Harga
                            </th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wider">Stok
                            </th>
                            <th class="text-center py-3 px-4 text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($products as $product)
                            <tr class="hover:bg-gray-50 transition-all table-row">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 border border-gray-100">
                                            @if($product['image'] ?? null)
                                                <img src="{{ env('BACKEND_URL') . '/storage/' . $product['image'] }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-sm font-bold text-gray-900 search-name">{{ $product['name'] }}</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-sm text-gray-700 search-seller">
                                    {{ $product['seller']['name'] ?? 'Guest Seller' }}
                                </td>
                                <td class="py-4 px-4 text-sm font-bold text-amber-700">
                                    Rp {{ number_format($product['price'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-sm font-bold">
                                    @if($product['stock'] == 0)
                                        <span class="text-red-600 bg-red-50 px-2 py-1 rounded-lg">Habis</span>
                                    @elseif($product['stock'] <= 5)
                                        <span class="text-red-500 bg-red-50 px-2 py-1 rounded-lg">{{ $product['stock'] }}</span>
                                    @else
                                        <span class="text-gray-700">{{ $product['stock'] }}</span>
                                    @endif
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('admin.products.show', $product['id']) }}"
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-100 rounded-lg transition-all"
                                            title="Detail Produk">
                                            <i data-lucide="eye" class="w-4 h-4 text-emerald-600"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 text-sm italic">
                                    Belum ada produk yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>     function filterTable() {
            const input = document.getElementById('searchInput'); const filter = input.value.toLowerCase(); const table = document.getElementById('productTable'); const tr = table.getElementsByClassName('table-row');
            for (let i = 0; i < tr.length; i++) {
                const name = tr[i].getElementsByClassName('search-name')[0].textContent.toLowerCase(); const seller = tr[i].getElementsByClassName('search-seller')[0].textContent.toLowerCase();
                if (name.includes(filter) || seller.includes(filter)) { tr[i].style.display = ""; } else { tr[i].style.display = "none"; }
            }
        }
    </script>
@endsection