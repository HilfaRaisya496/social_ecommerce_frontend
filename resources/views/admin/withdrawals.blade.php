@extends('layouts.admin')

@section('header_title', 'Pencairan Dana (Withdrawals)')
@section('header_subtitle', 'Kelola permintaan penarikan dana dari penjual')

@section('content')
<div class="space-y-6">
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 class="font-bold text-gray-900">Daftar Permintaan Pencairan Dana</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 font-semibold text-gray-600 text-sm">Tanggal</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Penjual</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Nominal</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Bank Tujuan</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Status</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($withdrawals as $w)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="p-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($w['created_at'])->format('d M Y, H:i') }}</td>
                            <td class="p-4 text-sm font-bold text-gray-900">{{ $w['user']['name'] ?? 'Penjual Tidak Diketahui' }}</td>
                            <td class="p-4 text-sm font-bold text-emerald-600">Rp {{ number_format($w['amount'], 0, ',', '.') }}</td>
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
                            <td class="p-4 text-right space-x-2">
                                @if($w['status'] == 'pending')
                                    <!-- Tombol Proses -->
                                    <button onclick="openProcessModal({{ json_encode($w) }})" class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i> Proses
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 text-sm">Tidak ada permintaan pencairan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Proses Pencairan -->
<div id="processModal" class="fixed inset-0 z-50 hidden bg-gray-900/50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden my-8">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-900 text-lg">Proses Pencairan Dana</h3>
            <button onclick="document.getElementById('processModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <div class="p-5 bg-amber-50 border-b border-amber-100">
            <p class="text-sm text-amber-800 font-medium mb-2"><i data-lucide="info" class="w-4 h-4 inline-block mr-1 align-middle"></i> Instruksi untuk Admin:</p>
            <ol class="list-decimal pl-5 text-sm text-amber-700 space-y-1">
                <li>Buka aplikasi M-Banking Anda.</li>
                <li>Transfer ke rekening penjual di bawah ini.</li>
                <li>Ambil *screenshot* (tangkapan layar) bukti transfer berhasil.</li>
                <li>Unggah bukti transfer pada form di bawah.</li>
            </ol>
        </div>

        <div class="p-5 space-y-4 border-b border-gray-100">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div class="grid grid-cols-2 gap-y-2 text-sm">
                    <div class="text-gray-500">Nominal Transfer:</div>
                    <div class="font-bold text-lg text-emerald-600" id="modalAmount">Rp 0</div>
                    
                    <div class="text-gray-500">Bank Tujuan:</div>
                    <div class="font-bold text-gray-900" id="modalBank">Bank</div>
                    
                    <div class="text-gray-500">Nomor Rekening:</div>
                    <div class="font-bold text-gray-900 flex items-center gap-2">
                        <span id="modalRekening">123</span>
                        <button onclick="copyRekening()" class="text-emerald-600 hover:text-emerald-700"><i data-lucide="copy" class="w-4 h-4"></i></button>
                    </div>
                    
                    <div class="text-gray-500">Atas Nama:</div>
                    <div class="font-bold text-gray-900" id="modalName">Nama</div>
                </div>
            </div>
        </div>

        <!-- Form Setuju -->
        <form id="approveForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 border-b border-emerald-100 bg-emerald-50/30">
            @csrf
            <p class="text-sm font-bold text-emerald-800">Setujui Pencairan</p>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Transfer *</label>
                <input type="file" name="receipt_image" accept="image/*" required class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Admin (Opsional)</label>
                <input type="text" name="admin_note" placeholder="Contoh: Sudah ditransfer via BI-FAST" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-colors shadow-sm shadow-emerald-200 flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i> Konfirmasi Transfer Berhasil
                </button>
            </div>
        </form>

        <!-- Form Tolak -->
        <form id="rejectForm" method="POST" class="p-5 bg-red-50/50">
            @csrf
            <p class="text-sm font-bold text-red-800 mb-3">Atau Tolak Pencairan</p>
            <div class="flex gap-3 items-end">
                <div class="flex-1">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Alasan Penolakan *</label>
                    <input type="text" name="admin_note" required placeholder="Contoh: Nomor rekening tidak ditemukan" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <button type="submit" class="px-5 py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold rounded-xl transition-colors shrink-0">
                    Tolak
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentRekening = '';

    function openProcessModal(data) {
        document.getElementById('processModal').classList.remove('hidden');
        
        // Format Rupiah
        let rupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.amount);
        
        document.getElementById('modalAmount').innerText = rupiah;
        document.getElementById('modalBank').innerText = data.bank_name;
        document.getElementById('modalRekening').innerText = data.account_number;
        document.getElementById('modalName').innerText = data.account_name;
        
        currentRekening = data.account_number;

        // Set action URLs
        document.getElementById('approveForm').action = `{{ url('admin/withdrawals') }}/${data.id}/approve`;
        document.getElementById('rejectForm').action = `{{ url('admin/withdrawals') }}/${data.id}/reject`;
    }

    function copyRekening() {
        navigator.clipboard.writeText(currentRekening).then(() => {
            alert('Nomor Rekening berhasil disalin!');
        });
    }
</script>
@endsection
