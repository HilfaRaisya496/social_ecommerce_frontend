@extends('layouts.seller')

@section('header_title', 'Riwayat Transaksi')

@section('content')
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Total Pendapatan</p>
                <h3 class="text-3xl font-bold text-green-600 mb-2">{{ $stats['total_revenue'] }}</h3>
                <p class="text-sm text-gray-500 font-medium italic">Pesanan Selesai</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Saldo Tertunda</p>
                <h3 class="text-3xl font-bold text-amber-500 mb-2">{{ $stats['pending_revenue'] }}</h3>
                <p class="text-sm text-gray-500 font-medium italic">Pesanan Sedang Berjalan</p>
            </div>
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
                <p class="text-sm text-gray-600 mb-1">Biaya Platform</p>
                <h3 class="text-3xl font-bold text-red-500 mb-2">{{ $stats['platform_fees'] }}</h3>
                <p class="text-sm text-gray-500 font-medium italic">Biaya Layanan Admin</p>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Riwayat Transaksi</h2>
                    <p class="text-sm text-gray-500">Lihat semua riwayat transaksi pembayaran Anda</p>
                </div>

            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">ID
                                Transaksi</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Pesanan</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Pelanggan</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Bruto</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Potongan</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Netto</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Status</th>
                            <th class="text-left py-4 px-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="py-4 px-4 text-xs font-medium text-gray-500">{{ $trx['transaction_id'] }}</td>
                                <td class="py-4 px-4 text-sm font-bold text-gray-900">{{ $trx['order_number'] }}</td>
                                <td class="py-4 px-4 text-sm text-gray-700 font-medium">{{ $trx['customer'] }}</td>
                                <td class="py-4 px-4 text-sm text-gray-900 font-bold">{{ $trx['amount'] }}</td>
                                <td class="py-4 px-4 text-sm text-red-500 font-medium">- {{ $trx['fee'] }}</td>
                                <td class="py-4 px-4 text-sm text-green-600 font-bold italic">{{ $trx['net'] }}</td>
                                <td class="py-4 px-4">
                                    <span
                                        class="px-2.5 py-1 rounded-lg border {{ $trx['status'] === 'completed' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-amber-50 text-amber-600 border-amber-100' }} text-[10px] font-bold uppercase tracking-wider">
                                        {{ $trx['status'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-[11px] text-gray-400 whitespace-nowrap">{{ $trx['date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4">
                                            <i data-lucide="receipt" class="w-8 h-8 text-gray-200"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">Belum ada transaksi</h3>
                                        <p class="text-xs text-gray-500 mt-1">Transaksi pembayaran Anda akan muncul di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection