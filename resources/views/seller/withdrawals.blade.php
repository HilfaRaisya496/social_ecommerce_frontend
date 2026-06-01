@extends('layouts.seller')

@section('header_title', 'Saldo & Penarikan Dana')
@section('header_subtitle', 'Kelola saldo Anda dan tarik dana ke rekening bank')

@section('content')
<div class="space-y-6">
    <!-- Balance Card -->
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl p-6 shadow-lg shadow-emerald-200/50 text-white flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <p class="text-emerald-100 font-medium mb-1">Total Saldo Tersedia</p>
            <h2 class="text-4xl font-bold tracking-tight">Rp {{ number_format($balance, 0, ',', '.') }}</h2>
            <p class="text-xs text-emerald-200 mt-2"><i data-lucide="info" class="w-3 h-3 inline-block mr-1"></i>Saldo bisa ditarik ke rekening Anda</p>
        </div>
        <div>
            <button onclick="document.getElementById('withdrawModal').classList.remove('hidden')" class="bg-white px-6 py-3 rounded-xl font-bold hover:bg-emerald-50 transition-colors shadow-sm flex items-center gap-2" style="color: #065f46;">
                <i data-lucide="banknote" class="w-5 h-5"></i> Tarik Dana Sekarang
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
            <p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-900">Riwayat Penarikan Dana</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 font-semibold text-gray-600 text-sm">Tanggal</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Nominal</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Bank Tujuan</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Catatan Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($w['created_at'])->format('d M Y, H:i') }}</td>
                            <td class="p-4 text-sm font-bold text-gray-900">Rp {{ number_format($w['amount'], 0, ',', '.') }}</td>
                            <td class="p-4 text-sm text-gray-600">
                                <div class="font-medium text-gray-900">{{ $w['bank_name'] }}</div>
                                <div class="text-xs">{{ $w['account_number'] }} a.n {{ $w['account_name'] }}</div>
                            </td>
                            <td class="p-4">
                                @if($w['status'] == 'pending')
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200">Menunggu</span>
                                @elseif($w['status'] == 'approved')
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold border border-emerald-200">Berhasil</span>
                                @else
                                    <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-200">Ditolak</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-500">
                                {{ $w['admin_note'] ?? '-' }}
                                @if($w['receipt_image'] ?? false)
                                    <br><a href="{{ rtrim(env('BACKEND_API_URL'), '/api') . '/storage/' . $w['receipt_image'] }}" target="_blank" class="text-emerald-600 text-xs font-bold hover:underline">Lihat Bukti Transfer</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 text-sm">Belum ada riwayat penarikan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Penarikan -->
<div id="withdrawModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-lg">Tarik Dana</h3>
            <button onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form action="{{ route('seller.withdrawals.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            
            <div class="bg-blue-50 border border-blue-100 p-3 rounded-xl flex items-start gap-2 mb-4">
                <i data-lucide="clock" class="w-5 h-5 text-blue-600 mt-0.5 shrink-0"></i>
                <p class="text-xs text-blue-800">
                    <strong>Jam Operasional:</strong> Pencairan dana hanya diproses pada pukul <span class="font-bold">07:00 - 21:00 WIB</span>.
                </p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Penarikan (Rp)</label>
                <input type="number" id="withdrawAmount" name="amount" min="10000" max="{{ $balance }}" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <p class="text-xs text-gray-500 mt-1">Minimal penarikan Rp 10.000. Sisa Saldo: Rp {{ number_format($balance, 0, ',', '.') }}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="bank_name" placeholder="Contoh: BCA, Mandiri, BRI" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="account_number" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama (Pemilik Rekening)</label>
                    <input type="text" name="account_name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('withdrawModal').classList.add('hidden')" class="px-5 py-2 text-gray-600 font-medium hover:bg-gray-100 rounded-xl transition-colors">Batal</button>
                <button type="submit" id="withdrawSubmitBtn" disabled class="px-5 py-2 bg-emerald-600 text-white font-medium rounded-xl transition-colors shadow-sm opacity-50 cursor-not-allowed">Ajukan Penarikan</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('withdrawAmount');
        const submitBtn = document.getElementById('withdrawSubmitBtn');
        const maxBalance = {{ $balance }};

        amountInput.addEventListener('input', function() {
            const val = parseFloat(this.value);
            if (val >= 10000 && val <= maxBalance) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.add('hover:bg-emerald-700', 'shadow-emerald-200');
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:bg-emerald-700', 'shadow-emerald-200');
            }
        });
    });
</script>
@endsection
