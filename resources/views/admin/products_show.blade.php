@extends('layouts.admin')

@section('header_title', 'Detail Produk')

@section('content')
    <div class="mb-4">
        <a href="{{ url()->previous() }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-amber-600 transition-all font-bold">
            <i data-lucide="arrow-left" class="w-4 h-4 text-emerald-600"></i>
            Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Images & Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <div class="flex flex-col md:flex-row gap-8">
                    <!-- Image -->
                    <div class="w-full md:w-80 h-80 rounded-2xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                        @if($product['image'] ?? null)
                            <img src="{{ env('BACKEND_URL') . '/storage/' . $product['image'] }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <i data-lucide="package" class="w-12 h-12 mb-2"></i>
                                <p class="text-xs">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Basic Info -->
                    <div class="flex-1">
                        <div class="mb-4">
                            <span
                                class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold uppercase tracking-wider">
                                {{ $product['category'] ?? 'General' }}
                            </span>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $product['name'] }}</h2>
                        <p class="text-3xl font-extrabold text-amber-600 mb-6">
                            Rp {{ number_format($product['price'], 0, ',', '.') }}
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Stok Tersedia</p>
                                <p
                                    class="text-lg font-bold {{ ($product['stock'] ?? 0) <= 5 ? 'text-red-500' : 'text-gray-900' }}">
                                    {{ $product['stock'] ?? 0 }} Unit
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-[10px] uppercase font-bold text-gray-400 mb-1">Total Terjual</p>
                                <p class="text-lg font-bold text-gray-900">{{ $product['total_sales'] ?? 0 }} Unit</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="align-left" class="w-5 h-5 text-amber-500"></i>
                    Deskripsi Produk
                </h3>
                <div class="prose prose-amber max-w-none text-gray-600 text-sm leading-relaxed">
                    {!! nl2br(e($product['description'] ?? 'Tidak ada deskripsi untuk produk ini.')) !!}
                </div>
            </div>
        </div>

        <!-- Seller & Actions -->
        <div class="space-y-6">
            <!-- Information Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest">Informasi Penjual</h3>
                <div class="flex items-center gap-4 mb-6">
                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 font-bold overflow-hidden border border-amber-100">
                        @if($product['seller']['profile_image'] ?? null)
                            <img src="{{ Str::startsWith($product['seller']['profile_image'], 'http') ? $product['seller']['profile_image'] : env('BACKEND_URL') . '/storage/' . $product['seller']['profile_image'] }}"
                                class="w-full h-full object-cover">
                        @else
                            {{ substr($product['seller']['name'] ?? 'S', 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $product['seller']['name'] ?? 'Unknown Seller' }}</p>
                        <p class="text-xs text-gray-500">{{ $product['seller']['email'] ?? '' }}</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <a href="{{ route('admin.users.show', $product['seller_id']) }}"
                        class="w-full py-3 px-4 bg-gray-50 text-gray-700 rounded-xl hover:bg-gray-100 font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="user" class="w-4 h-4"></i>
                        Lihat Profil Penjual
                    </a>
                    <a href="{{ route('admin.chats') }}?user_id={{ $product['seller_id'] }}"
                        class="w-full py-3 px-4 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 font-bold text-sm transition-all flex items-center justify-center gap-2">
                        <i data-lucide="message-square" class="w-4 h-4"></i>
                        Hubungi Penjual
                    </a>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 rounded-2xl p-6 border border-red-100">
                <h3 class="text-sm font-bold text-red-600 mb-4 uppercase tracking-widest">Moderasi Produk</h3>
                <p class="text-xs text-red-500 mb-4 leading-relaxed">
                    Hapus produk ini jika melanggar kebijakan marketplace Zeven. Aksi ini tidak dapat dibatalkan.
                </p>
                <form id="deleteProductForm" action="{{ route('admin.products.delete', $product['id']) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete()"
                        class="w-full py-3 px-4 bg-red-600 text-white rounded-xl hover:bg-red-700 font-bold text-sm transition-all shadow-lg shadow-red-100 flex items-center justify-center gap-2">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        Hapus Produk Secara Paksa
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Produk Paksa?',
                text: "Apakah Anda benar-benar yakin ingin menghapus produk ini? Aksi ini akan menghapus data secara permanen dari Zeven Marketplace.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus Sekarang!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-3 font-bold',
                    cancelButton: 'rounded-xl px-6 py-3 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Product deletion confirmed, submitting form...');
                    document.getElementById('deleteProductForm').submit();
                }
            })
        }
    </script>
@endpush