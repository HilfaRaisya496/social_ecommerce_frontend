@extends('layouts.admin')

@section('header_title', 'Detail Transaksi')

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.transactions') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-amber-600 transition-all">
            <i data-lucide="arrow-left" class="w-4 h-4 text-emerald-600 font-bold"></i>
            Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <div class="space-y-6">
        <!-- Main Info Card -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 pb-8 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center">
                        <i data-lucide="receipt" class="w-7 h-7 text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Order #{{ str_pad($order['id'], 5, '0', STR_PAD_LEFT) }}</h2>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2">
                    @php
                        $status = strtolower($order['status']);
                        $badgeClass = match ($status) {
                            'completed', 'success' => 'bg-emerald-100 text-emerald-600 border-emerald-200',
                            'pending' => 'bg-amber-100 text-amber-600 border-amber-200',
                            'cancelled', 'failed' => 'bg-red-100 text-red-600 border-red-200',
                            'processed' => 'bg-blue-100 text-blue-600 border-blue-200',
                            'shipped' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                            default => 'bg-gray-100 text-gray-600 border-gray-200'
                        };
                    @endphp
                    <span class="px-4 py-1.5 {{ $badgeClass }} border rounded-full text-xs font-bold uppercase tracking-widest shadow-sm">
                        {{ $order['status'] }}
                    </span>
                    <p class="text-xs text-gray-400">Metode: {{ $order['payment_method'] ?? 'Transfer Bank' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Buyer Info -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Pembeli</h3>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-50">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold overflow-hidden">
                            @if($order['buyer']['profile_image'] ?? null)
                                <img src="{{ Str::startsWith($order['buyer']['profile_image'], 'http') ? $order['buyer']['profile_image'] : env('BACKEND_URL') . '/storage/' . $order['buyer']['profile_image'] }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($order['buyer']['name'] ?? 'B', 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $order['buyer']['name'] ?? 'Unknown Buyer' }}</p>
                            <p class="text-xs text-gray-500">{{ $order['buyer']['phone'] ?? 'No Phone' }}</p>
                        </div>
                        @if($order['buyer_id'])
                        <a href="{{ route('admin.users.show', $order['buyer_id']) }}" class="ml-auto p-2 text-gray-400 hover:text-amber-600 hover:bg-white rounded-lg transition-all shadow-sm">
                            <i data-lucide="eye" class="w-4 h-4 text-emerald-600"></i>
                        </a>
                        @endif
                    </div>
                    <div class="mt-4 px-4 py-3 bg-white rounded-2xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-2">Alamat Pengiriman</p>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $order['shipping_address'] ?? 'Tidak ada data alamat.' }}</p>
                    </div>
                </div>

                <!-- Seller Info -->
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Informasi Penjual</h3>
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-50">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 font-bold overflow-hidden border border-amber-100">
                             @if($order['seller']['profile_image'] ?? null)
                                <img src="{{ Str::startsWith($order['seller']['profile_image'], 'http') ? $order['seller']['profile_image'] : env('BACKEND_URL') . '/storage/' . $order['seller']['profile_image'] }}" class="w-full h-full object-cover">
                            @else
                                {{ substr($order['seller']['name'] ?? 'U', 0, 1) }}
                            @endif
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $order['seller']['name'] ?? 'Unknown Seller' }}</p>
                            <p class="text-xs text-gray-500">{{ $order['seller']['phone'] ?? 'No Phone' }}</p>
                        </div>
                        @if($order['seller_id'])
                        <a href="{{ route('admin.users.show', $order['seller_id']) }}" class="ml-auto p-2 text-gray-400 hover:text-amber-600 hover:bg-white rounded-lg transition-all shadow-sm">
                            <i data-lucide="eye" class="w-4 h-4 text-emerald-600"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Verification Card -->
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Verifikasi Pembayaran</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Payment details and actions -->
                <div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Status Pembayaran saat ini:</span>
                            @php
                                $payStatus = strtolower($order['transaction']['payment_status'] ?? 'pending');
                                $payStatusLabel = match ($payStatus) {
                                    'success' => 'SUCCESS / BERHASIL',
                                    'failed' => 'FAIL / GAGAL',
                                    'checking' => 'CHECKING ADMIN / VERIFIKASI',
                                    default => 'PENDING'
                                };
                                $payBadgeClass = match ($payStatus) {
                                    'success' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'checking' => 'bg-amber-100 text-amber-700 border-amber-200 animate-pulse',
                                    'failed' => 'bg-red-100 text-red-700 border-red-200',
                                    default => 'bg-gray-100 text-gray-700 border-gray-200'
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $payBadgeClass }}">
                                {{ $payStatusLabel }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Metode Pembayaran:</span>
                            <span class="text-sm font-bold text-gray-800">{{ $order['transaction']['payment_method'] ?? 'Transfer Bank' }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-sm text-gray-500">Waktu Pembayaran:</span>
                            <span class="text-sm text-gray-800">{{ ($order['transaction']['paid_at'] ?? null) ? \Carbon\Carbon::parse($order['transaction']['paid_at'])->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>

                    <!-- Admin confirmation actions -->
                    @if(isset($order['transaction']) && $order['transaction']['payment_status'] === 'checking')
                        <div class="mt-8 p-4 bg-amber-50/50 rounded-2xl border border-amber-100">
                            <p class="text-xs font-medium text-amber-800 mb-4">
                                <i class="inline-block w-4 h-4 mr-1 text-amber-600 align-middle" data-lucide="alert-circle"></i>
                                Pembeli telah mengirimkan bukti transfer. Silakan periksa gambar bukti transfer di sebelah kanan, kemudian konfirmasi status pembayaran ini.
                            </p>
                            
                            <div class="flex gap-4">
                                <form action="{{ route('admin.transactions.confirm', $order['id']) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="payment_status" value="success">
                                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-100">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Terima (Success)
                                    </button>
                                </form>

                                <form action="{{ route('admin.transactions.confirm', $order['id']) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="payment_status" value="failed">
                                    <button type="submit" class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2 shadow-lg shadow-red-100">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        Tolak (Fail)
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="mt-8 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <p class="text-xs text-gray-500">
                                Status pembayaran ini adalah <strong>{{ strtoupper($order['transaction']['payment_status'] ?? 'PENDING') }}</strong>. 
                                @if(!isset($order['transaction']['payment_proof']) || !$order['transaction']['payment_proof'])
                                    Menunggu pembeli melakukan transfer dan mengunggah bukti bayar.
                                @else
                                    Verifikasi telah diselesaikan oleh Admin.
                                @endif
                            </p>
                            @if(isset($order['transaction']))
                            <div class="flex gap-4 mt-4">
                                <form action="{{ route('admin.transactions.confirm', $order['id']) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="payment_status" value="success">
                                    <button type="submit" class="w-full py-2 bg-white hover:bg-gray-50 text-emerald-600 border border-gray-200 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-2">
                                        Ubah Ke Sukses
                                    </button>
                                </form>
                                <form action="{{ route('admin.transactions.confirm', $order['id']) }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="payment_status" value="failed">
                                    <button type="submit" class="w-full py-2 bg-white hover:bg-gray-50 text-red-600 border border-gray-200 rounded-xl font-bold transition-all text-xs flex items-center justify-center gap-2">
                                        Ubah Ke Gagal
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Proof image display -->
                <div class="flex flex-col items-center justify-center p-6 bg-gray-50 rounded-2xl border border-gray-100 min-h-[250px]">
                    @if(isset($order['transaction']['payment_proof']) && $order['transaction']['payment_proof'])
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Bukti Transfer Pembeli</p>
                        <a href="{{ env('BACKEND_URL', 'http://192.168.100.6:8001') . '/storage/' . $order['transaction']['payment_proof'] }}" target="_blank" class="block group relative rounded-xl overflow-hidden shadow-md max-w-[300px]">
                            <img src="{{ env('BACKEND_URL', 'http://192.168.100.6:8001') . '/storage/' . $order['transaction']['payment_proof'] }}" class="max-h-[300px] w-auto object-contain transition-transform group-hover:scale-105">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all">
                                <span class="text-white text-xs font-bold flex items-center gap-1">
                                    <i data-lucide="maximize-2" class="w-4 h-4"></i> Perbesar Gambar
                                </span>
                            </div>
                        </a>
                    @else
                        <div class="text-center p-6">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="image-off" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h4 class="text-sm font-bold text-gray-700 mb-1">Bukti Transfer Belum Ada</h4>
                            <p class="text-xs text-gray-500 max-w-[200px] mx-auto">Pembeli belum mengunggah foto bukti pembayaran untuk transaksi ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Item Pesanan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="py-4 px-8 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Produk</th>
                            <th class="py-4 px-8 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Harga</th>
                            <th class="py-4 px-8 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jumlah</th>
                            <th class="py-4 px-8 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($order['items'] ?? [] as $item)
                        <tr class="hover:bg-gray-50/50 transition-all">
                            <td class="py-4 px-8">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                        @if($item['product']['image'] ?? null)
                                            <img src="{{ str_starts_with($item['product']['image'], 'http') ? $item['product']['image'] : (env('BACKEND_URL', 'http://192.168.100.6:8001') . '/storage/' . $item['product']['image']) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center"><i data-lucide="package" class="w-4 h-4 text-gray-300"></i></div>
                                        @endif
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">{{ $item['product']['name'] ?? 'Unknown Product' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-8 text-center text-sm text-gray-700">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                            <td class="py-4 px-8 text-center text-sm font-bold text-gray-900">{{ $item['quantity'] }}x</td>
                            <td class="py-4 px-8 text-right text-sm font-bold text-gray-900">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="p-8 bg-gray-50/50 flex justify-end">
                <div class="w-full md:w-80 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Subtotal (Harga Barang)</span>
                        <span class="font-bold text-gray-900">Rp {{ number_format($order['total_price'], 0, ',', '.') }}</span>
                    </div>
                    @if(isset($order['discount_amount']) && $order['discount_amount'] > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-red-500">Potongan Diskon</span>
                        <span class="font-bold text-red-600">-Rp {{ number_format($order['discount_amount'], 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <span class="text-emerald-600">Pendapatan Biaya Layanan (100% Admin)</span>
                        <span class="font-bold text-emerald-700">+Rp 1.000</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total Pembayaran Pembeli</span>
                        <span class="text-xl font-extrabold text-amber-600">Rp {{ number_format($order['final_price'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
