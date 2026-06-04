@extends('layouts.admin')

@section('header_title', 'Voucher & Promosi')

@section('content')
    <div class="space-y-6">
        {{-- Header & Action --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">Daftar Voucher Global</h2>
                <p class="text-sm text-gray-500">Kelola kupon diskon dan promosi di seluruh marketplace</p>
            </div>
            <a href="{{ route('admin.vouchers.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition-all font-medium shadow-sm shadow-amber-200 active:scale-95">
                <i data-lucide="plus" class="w-5 h-5 text-emerald-900 font-bold"></i>
                Buat Voucher Baru
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="flex items-center gap-3 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100">
            <i data-lucide="check-circle" class="w-5 h-5"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
        @endif
        @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-red-50 text-red-600 rounded-xl border border-red-100">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
        @endif

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
                        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Cari kode voucher..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                    </div>
                    <div class="bg-amber-50 text-amber-600 px-4 py-2.5 rounded-xl text-sm font-medium border border-amber-100 flex items-center gap-2">
                        <i data-lucide="ticket-percent" class="w-4 h-4"></i>
                        Voucher Aktif: {{ count($vouchers) }}
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto max-h-[500px] overflow-y-auto custom-scrollbar">
                <table class="w-full" id="voucherTable">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Diskon (%)</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Maks. Potongan</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Kupon (Terpakai/Total)</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Masa Berlaku</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($vouchers as $voucher)
                            <tr class="hover:bg-gray-50 transition-all table-row">
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg font-mono font-bold text-sm search-code">
                                        {{ $voucher['code'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $voucher['discount_percent'] }}%</td>
                                <td class="py-4 px-6 text-sm text-gray-700">Rp {{ number_format($voucher['max_discount'] ?? 0, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-sm text-gray-600">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 w-16 bg-gray-100 rounded-full overflow-hidden">
                                            @php
                                                $quota = $voucher['quota'] ?? 1;
                                                $used = $voucher['used'] ?? 0;
                                                $progress = ($quota > 0) ? ($used / $quota) * 100 : 0;
                                            @endphp
                                            <div class="bg-amber-500 h-full" style="width: {{ $progress }}%"></div>
                                        </div>
                                        <span>{{ $used }}/{{ $quota }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-xs text-gray-500">
                                    @if(!empty($voucher['start_date']) && !empty($voucher['end_date']))
                                        {{ \Carbon\Carbon::parse($voucher['start_date'])->format('d M') }} - {{ \Carbon\Carbon::parse($voucher['end_date'])->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    @php
                                        $endDate = !empty($voucher['end_date']) ? \Carbon\Carbon::parse($voucher['end_date']) : null;
                                        $isExpired = $endDate ? $endDate->isPast() : false;
                                        $isFull = ($voucher['used'] ?? 0) >= ($voucher['quota'] ?? 0);
                                    @endphp
                                    @if($isExpired)
                                        <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-[10px] font-bold uppercase">Expired</span>
                                    @elseif($isFull)
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-bold uppercase">Full</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-bold uppercase">Active</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.vouchers.edit', $voucher['id']) }}" class="p-2 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Edit Voucher">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.vouchers.destroy', $voucher['id']) }}" method="POST" class="inline-block delete-voucher-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete(this)" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus Voucher">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 text-sm italic">
                                    Belum ada voucher yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: 'Hapus Voucher?',
                text: "Voucher yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#f3f4f6',
                cancelButtonText: '<span style="color: #374151; font-weight: bold;">Batal</span>',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            })
        }

        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('voucherTable');
            const tr = table.getElementsByClassName('table-row');

            for (let i = 0; i < tr.length; i++) {
                const codeCell = tr[i].getElementsByClassName('search-code')[0];
                if (codeCell) {
                    const codeText = codeCell.textContent.toLowerCase();
                    if (codeText.includes(filter)) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }
    </script>
@endsection