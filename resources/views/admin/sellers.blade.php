@extends('layouts.admin')

@section('header_title', 'Manajemen Seller')

@section('content')
    <div class="space-y-6">
        @php
            $newSellersThisMonth = collect($sellers)->filter(function($seller) {
                return \Carbon\Carbon::parse($seller['created_at'])->isCurrentMonth();
            })->count();
        @endphp
        {{-- Stats Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="store" class="w-6 h-6 text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Penjual</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ count($sellers) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="package" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Toko Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ count($sellers) }}</h3>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Seller Baru</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $newSellersThisMonth }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 p-6">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Daftar Penjual</h2>
                    <p class="text-sm text-gray-500">Monitor performa dan manajemen akun toko</p>
                </div>
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari seller..."
                        class="w-full md:w-64 pl-9 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                </div>
            </div>

            <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                <table class="w-full" id="sellerTable">
                    <thead class="sticky top-0 z-10 bg-white shadow-sm">
                        <tr class="bg-gray-50/50">
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Toko /
                                Seller</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kontak
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Lokasi
                            </th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Terdaftar</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="text-center py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($sellers as $seller)
                            <tr class="hover:bg-gray-50 transition-all table-row">
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-amber-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-amber-200 text-amber-600 font-bold text-sm uppercase">
                                            @if($seller['profile_image'] ?? null)
                                                <img src="{{ Str::startsWith($seller['profile_image'], 'http') ? $seller['profile_image'] : env('BACKEND_URL') . '/storage/' . $seller['profile_image'] }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                {{ substr($seller['name'], 0, 1) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 search-name">{{ $seller['name'] }}</p>
                                            <span
                                                class="text-[10px] px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded uppercase font-bold">Seller</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-sm text-gray-700 font-medium search-email">{{ $seller['email'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $seller['phone'] ?? '-' }}</p>
                                </td>
                                <td class="py-4 px-6">
                                    <p class="text-xs text-gray-600 line-clamp-1 max-w-[150px]">
                                        {{ $seller['address'] ?? 'Belum diatur' }}
                                    </p>
                                </td>
                                <td class="py-4 px-6 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($seller['created_at'])->format('d M Y') }}
                                </td>
                                <td class="py-4 px-6">
                                    <span
                                        class="px-2.5 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-bold uppercase">
                                        Verified
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.users.show', $seller['id']) }}"
                                            class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all"
                                            title="Detail Profile">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <a href="{{ route('admin.chats') }}?user_id={{ $seller['id'] }}"
                                            class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
                                            title="Chat User">
                                            <i data-lucide="message-square" class="w-4 h-4 text-emerald-600"></i>
                                        </a>

                                        <form id="delete-form-{{ $seller['id'] }}"
                                            action="{{ route('admin.users.delete', $seller['id']) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete({{ $seller['id'] }})"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                title="Hapus User">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400 text-sm italic">
                                    Belum ada penjual yang terdaftar.
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
            const table = document.getElementById('sellerTable');
            const tr = table.getElementsByClassName('table-row');

            for (let i = 0; i < tr.length; i++) {
                const name = tr[i].getElementsByClassName('search-name')[0].textContent.toLowerCase();
                const email = tr[i].getElementsByClassName('search-email')[0].textContent.toLowerCase();

                if (name.includes(filter) || email.includes(filter)) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }
    </script>
@endsection

@push('scripts')
    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Hapus Penjual?',
                text: "Semua produk dan data toko terkait akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5', cancelButton: 'rounded-xl px-6 py-2.5' }
            }).then((result) => { if (result.isConfirmed) { document.getElementById('delete-form-' + userId).submit(); } })
        }
    </script>
@endpush