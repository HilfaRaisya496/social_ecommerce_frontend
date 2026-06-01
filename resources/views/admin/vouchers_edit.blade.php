@extends('layouts.admin')

@section('header_title', 'Edit Voucher')

@section('content')
    <div class="space-y-6">
        <a href="{{ route('admin.vouchers') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-amber-600 transition-all font-bold">
            <i data-lucide="arrow-left" class="w-4 h-4 text-emerald-600"></i>
            Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
        <div class="p-6 border-b border-gray-100 bg-amber-50/50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center">
                    <i data-lucide="edit-3" class="w-6 h-6 text-amber-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Edit Voucher</h3>
                    <p class="text-sm text-gray-500">Perbarui data voucher {{ $voucher['code'] }}</p>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.vouchers.update', $voucher['id']) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Flash Message Error --}}
            @if(session('error'))
                <div class="p-4 bg-red-50 border border-red-100 rounded-xl text-red-600 text-sm flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Voucher</label>
                <input type="text" name="code" value="{{ old('code', $voucher['code']) }}" placeholder="CONTOH: HEMAT50" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 uppercase font-mono text-lg">
                @error('code')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Diskon (%)</label>
                    <div class="relative">
                        <input type="number" name="discount_percent" value="{{ old('discount_percent', $voucher['discount_percent']) }}"
                            placeholder="Max 100" required
                            class="w-full pl-4 pr-10 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                    </div>
                    @error('discount_percent')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Maks. Potongan (Rp)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                        <input type="number" name="max_discount" value="{{ old('max_discount', $voucher['max_discount']) }}" placeholder="Ex: 50000"
                            required
                            class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    </div>
                    @error('max_discount')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kouta / Jumlah Kupon</label>
                <input type="number" name="quota" value="{{ old('quota', $voucher['quota']) }}" placeholder="Jumlah voucher tersedia" required
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                @error('quota')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($voucher['start_date'])->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    @error('start_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Berakhir</label>
                    <input type="date" name="end_date" value="{{ old('end_date', \Carbon\Carbon::parse($voucher['end_date'])->format('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500">
                    @error('end_date')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-6 flex justify-end gap-3">
                <a href="{{ route('admin.vouchers') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 font-bold transition-all">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-amber-600 text-white rounded-xl hover:bg-amber-700 font-bold transition-all shadow-lg shadow-amber-100 active:scale-95">
                    Perbarui Voucher
                </button>
            </div>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>
@endsection
