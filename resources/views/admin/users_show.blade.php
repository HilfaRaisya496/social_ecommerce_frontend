@extends('layouts.admin')

@section('header_title', 'User Detail')

@section('content')
    <div class="mb-4">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-amber-600 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4 text-emerald-600 font-bold"></i>
            Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <div class="space-y-6">

        <!-- Main Info Card -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center">
                <div
                    class="w-24 h-24 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 p-1 mb-4 shadow-lg shadow-amber-200">
                    <div class="w-full h-full rounded-2xl bg-white overflow-hidden flex items-center justify-center">
                        @php
                            $avatarUrl = $user['profile_image'];
                            if ($avatarUrl && !str_starts_with($avatarUrl, 'http')) {
                                $avatarUrl = config('services.api.base_url') . '/storage/' . $avatarUrl;
                            }
                        @endphp
                        @if($avatarUrl)
                            <img src="{{ $avatarUrl }}" class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center">
                                <span class="text-2xl font-bold text-white uppercase">{{ substr($user['name'], 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">{{ $user['name'] }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ $user['email'] }}</p>
                <div class="flex items-center gap-2 mb-6">
                    <span
                        class="px-3 py-1 bg-amber-100 text-amber-600 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $user['role'] }}
                    </span>
                    <span
                        class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold uppercase tracking-wider">
                        Active
                    </span>
                </div>
                <div class="w-full border-t border-gray-50 pt-6 grid grid-cols-2 gap-4">
                    <div class="text-left">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Terdaftar Sejak</p>
                        <p class="text-sm font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($user['created_at'])->format('d M Y') }}
                        </p>
                    </div>
                    <div class="text-left">
                        <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Status Akun</p>
                        <p class="text-sm font-bold text-green-600">Terverifikasi</p>
                    </div>
                </div>
            </div>

            <!-- Detail Stats Cards -->
            <div class="lg:col-span-2 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                                <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Total Belanja</p>
                                <h3 class="text-2xl font-bold text-gray-900">{{ count($user['orders'] ?? []) }} Orders
                                </h3>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-amber-500 h-full w-[70%]"></div>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                                <i data-lucide="wallet" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Total Spend</p>
                                <h3 class="text-2xl font-bold text-gray-900">
                                    Rp
                                    {{ number_format(collect($user['orders'] ?? [])->sum('final_price'), 0, ',', '.') }}
                                </h3>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                            <div class="bg-emerald-500 h-full w-[45%]"></div>
                        </div>
                    </div>
                </div>

                <!-- Info List Card -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-amber-500"></i>
                        Informasi Kontak & Alamat
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-2">
                        <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-50">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-2 tracking-widest">Nomor Telepon</p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center">
                                    <i data-lucide="phone" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ $user['phone'] ?? 'Tidak tersedia' }}</p>
                            </div>
                        </div>
                        <div class="p-5 bg-gray-50/50 rounded-2xl border border-gray-50">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-2 tracking-widest">Alamat Pengiriman
                            </p>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-amber-600"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900">{{ $user['address'] ?? 'Belum diatur' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Tables -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Riwayat Transaksi Terakhir</h3>
                    <p class="text-sm text-gray-400">Monitoring aktivitas belanja user</p>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="py-4 px-8 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Order
                                ID</th>
                            <th class="py-4 px-8 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seller
                            </th>
                            <th class="py-4 px-8 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount
                            </th>
                            <th class="py-4 px-8 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status
                            </th>
                            <th class="py-4 px-8 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($user['orders'] ?? [] as $order)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="py-4 px-8 text-sm font-bold text-gray-900">
                                    #{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="py-4 px-8">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded bg-gray-100 flex items-center justify-center">
                                            <i data-lucide="store" class="w-3 h-3 text-gray-400"></i>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-700">{{ $order['seller']['name'] ?? 'Unknown Seller' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-8 text-sm font-bold text-amber-700">Rp
                                    {{ number_format($order['final_price'], 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-8">
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        {{ $order['status'] }}
                                    </span>
                                </td>
                                <td class="py-4 px-8 text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center text-gray-500 text-sm italic">Belum ada riwayat
                                    transaksi ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection