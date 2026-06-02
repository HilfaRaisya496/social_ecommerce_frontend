@extends('layouts.admin')

@section('header_title', 'Monitoring Transaksi')

    @section('content')
        <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-stat-card title="Total" value="{{ count($orders) }}" icon="receipt" iconBgColor="bg-gray-100"
                iconColor="text-gray-600" />
            <x-stat-card title="Volume" value="Rp{{ number_format(collect($orders)->sum('final_price')/1000, 0) }}k"
                icon="trending-up" iconBgColor="bg-amber-50" iconColor="text-amber-600" />
            <x-stat-card title="Pending" value="{{ collect($orders)->where('status', 'pending')->count() }}" icon="clock"
                iconBgColor="bg-emerald-50" iconColor="text-emerald-600" />
            <x-stat-card title="Proses" value="{{ collect($orders)->where('status', 'processed')->count() }}" icon="refresh-cw"
                iconBgColor="bg-blue-50" iconColor="text-blue-600" />
            <x-stat-card title="Dikirim" value="{{ collect($orders)->where('status', 'shipped')->count() }}" icon="truck"
                iconBgColor="bg-indigo-50" iconColor="text-indigo-600" />
            <x-stat-card title="Selesai" value="{{ collect($orders)->where('status', 'completed')->count() }}" icon="check-circle"
                iconBgColor="bg-green-50" iconColor="text-green-600" />
        </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Monitoring Transaksi</h2>
                        <p class="text-sm text-gray-500">Kelola dan pantau semua aliran dana marketplace</p>
                    </div>
                    <div class="relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari ID, Buyer, atau Seller..."
                            class="w-full md:w-64 pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                    </div>
                </div>

                <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                    <table class="w-full" id="transactionTable">
                        <thead class="sticky top-0 z-10 bg-white shadow-sm">
                            <tr class="bg-gray-50/50">
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Order
                                    ID</th>
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Alur
                                    Transaksi</th>
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Total
                                    Pembayaran</th>
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal
                                </th>
                                <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                                </th>
                                <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($orders as $order)
                                @php
                                    $status = strtolower($order['status']);
                                    $badgeClass = match ($status) {
                                        'completed', 'success' => 'bg-emerald-100 text-emerald-600',
                                        'pending' => 'bg-amber-100 text-amber-600',
                                        'cancelled', 'failed' => 'bg-red-100 text-red-600',
                                        default => 'bg-gray-100 text-gray-600'
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 transition-all table-row">
                                    <td class="py-4 px-6">
                                        <span
                                            class="text-sm font-bold text-gray-900 search-id">#{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <div class="text-left">
                                                <p class="text-xs text-gray-400 font-medium uppercase tracking-tighter">Buyer</p>
                                                <p class="text-sm font-bold text-gray-900 search-buyer">
                                                    {{ $order['buyer']['name'] ?? 'Unknown' }}</p>
                                            </div>
                                            <i data-lucide="arrow-right" class="w-3 h-3 text-gray-300"></i>
                                            <div class="text-left">
                                                <p class="text-xs text-gray-400 font-medium uppercase tracking-tighter">Seller</p>
                                                <p class="text-sm font-bold text-gray-700 search-seller">
                                                    {{ $order['seller']['name'] ?? 'Unknown' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-sm font-bold text-amber-700">Rp
                                            {{ number_format($order['final_price'], 0, ',', '.') }}</p>
                                        <p class="text-[10px] text-gray-400">Termasuk ongkir</p>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($order['created_at'])->timezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-2.5 py-1 {{ $badgeClass }} rounded-full text-[10px] font-bold uppercase">
                                            {{ $order['status'] }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ route('admin.transactions.show', $order['id']) }}"
                                                class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                                title="Detail Transaksi">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500 text-sm italic">
                                        Belum ada data transaksi yang tersedia.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            function filterTable() {
                const input = document.getElementById('searchInput');
                const filter = input.value.toLowerCase();
                const table = document.getElementById('transactionTable');
                const tr = table.getElementsByClassName('table-row');

                for (let i = 0; i < tr.length; i++) {
                    const id = tr[i].getElementsByClassName('search-id')[0].textContent.toLowerCase();
                    const buyer = tr[i].getElementsByClassName('search-buyer')[0].textContent.toLowerCase();
                    const seller = tr[i].getElementsByClassName('search-seller')[0].textContent.toLowerCase();

                    if (id.includes(filter) || buyer.includes(filter) || seller.includes(filter)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        </script>
    @endsection