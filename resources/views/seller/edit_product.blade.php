@extends('layouts.seller')

@section('header_title', 'Edit Produk')

@section('content')
    <div class="mb-6">
        <a href="{{ route('seller.products') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-emerald-700 transition-all font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4 text-amber-500"></i>
            Kembali ke Daftar Produk
        </a>
    </div>

    <div class="w-full">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            @if(session('error'))
                <div class="flex items-center gap-3 p-4 mb-6 bg-red-50 text-red-600 rounded-xl border border-red-100">
                    <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <p class="text-sm font-medium">{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('seller.products.update', $product['id']) }}" method="POST" enctype="multipart/form-data"
                class="space-y-8">
                @csrf
                @method('PUT')

                {{-- Foto Produk --}}
                <div class="p-6 bg-gray-50/50 rounded-2xl border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-4">Foto Produk Utama</label>
                    <div class="flex flex-col md:flex-row md:items-center gap-6">
                        <div class="relative group">
                            <div id="productImageInitial"
                                class="{{ !empty($product['image_url']) ? 'hidden' : '' }} w-32 h-32 bg-white rounded-2xl flex flex-col items-center justify-center text-gray-400 border-2 border-dashed border-gray-200">
                                <i data-lucide="image-plus" class="w-8 h-8 mb-2"></i>
                                <span class="text-[10px] font-bold uppercase">Unggah</span>
                            </div>
                            @if(!empty($product['image_url']))
                                <img id="productImagePreview" src="{{ $product['image_url'] }}"
                                    class="w-32 h-32 rounded-2xl object-cover border-2 border-emerald-600 shadow-xl"
                                    alt="Preview">
                            @else
                                <img id="productImagePreview" src=""
                                    class="w-32 h-32 rounded-2xl object-cover border-2 border-emerald-600 shadow-xl hidden"
                                    alt="Preview">
                            @endif
                        </div>
                        <div class="flex-1">
                            <input id="productImage" name="image" type="file" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-700 file:text-white hover:file:bg-emerald-800 transition-all cursor-pointer shadow-sm" />
                            <p class="text-[10px] text-gray-400 mt-2 italic">*Kosongkan jika tidak ingin mengubah foto</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Nama Produk --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                        <input type="text" name="name" required value="{{ $product['name'] ?? '' }}"
                            placeholder="Contoh: Sepatu Sneakers"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition-all shadow-sm" />
                    </div>

                    <!-- Gallery Images -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 text-sm font-bold mb-4">Galeri Foto Produk</label>
                        <div id="gallery-container" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4 mb-4">
                            @foreach($product['images'] ?? [] as $img)
                                @if(!($img['is_primary'] ?? false))
                                    <div class="relative group aspect-square">
                                        <img src="{{ $img['image_url'] }}"
                                            class="w-full h-full object-cover rounded-xl border border-gray-200">
                                        <button type="button" onclick="deleteImage({{ $img['id'] }}, this)"
                                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity shadow-lg">
                                            <i data-lucide="x" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                @endif
                            @endforeach

                            {{-- Add Button --}}
                            <label for="additional_images"
                                class="aspect-square border-2 border-dashed border-gray-200 rounded-xl flex flex-col items-center justify-center text-gray-400 hover:border-emerald-300 hover:text-emerald-600 transition-all cursor-pointer bg-gray-50/50">
                                <i data-lucide="plus" class="w-6 h-6 mb-1 text-amber-500"></i>
                                <span class="text-[10px] font-bold uppercase">Tambah</span>
                                <input id="additional_images" type="file" name="additional_images[]" class="hidden" multiple
                                    accept="image/*" onchange="previewGallery(event)">
                            </label>
                        </div>
                        <input type="hidden" name="delete_images" id="delete_images_input" value="[]">
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                        <div class="relative">
                            <select name="category" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition-all appearance-none bg-white shadow-sm">
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat['name'] }}" {{ ($product['category'] ?? '') === $cat['name'] ? 'selected' : '' }}>{{ $cat['name'] }}</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sisa Stok</label>
                        <input type="number" name="stock" required value="{{ $product['stock'] ?? '' }}" placeholder="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition-all shadow-sm" />
                    </div>

                    {{-- Harga --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Harga Jual</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-700 font-bold">Rp</span>
                            <input type="number" name="price" required
                                value="{{ $product['price_numeric'] ?? $product['price'] }}" placeholder="0"
                                class="w-full pl-12 pr-4 py-4 border border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition-all shadow-sm text-lg font-bold text-gray-900" />
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Produk</label>
                        <textarea name="description" rows="5" required placeholder="Jelaskan detail produk..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-2xl resize-none focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-600 transition-all shadow-sm">{{ $product['description'] ?? '' }}</textarea>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-4 pt-6">
                    <a href="{{ route('seller.products') }}"
                        class="flex-1 px-8 py-4 border border-gray-200 rounded-2xl text-gray-600 font-bold hover:bg-gray-50 transition-all text-center">
                        Batal
                    </a>
                    <button type="submit"
                        class="flex-[2] px-8 py-4 bg-gradient-to-r from-emerald-600 to-emerald-800 text-white rounded-2xl font-bold hover:from-emerald-700 hover:to-emerald-900 transition-all shadow-xl shadow-emerald-100 active:scale-[0.98] flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5 text-amber-400"></i>
                        Simpan Perubahan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const imageInput = document.getElementById('productImage');
        const preview = document.getElementById('productImagePreview');
        const initial = document.getElementById('productImageInitial');

        imageInput.addEventListener('change', () => {
            const file = imageInput.files[0];

            if (!file) {
                preview.classList.add('hidden');
                initial.classList.remove('hidden');
                preview.src = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                preview.src = event.target.result;
                preview.classList.remove('hidden');
                initial.classList.add('hidden');
                preview.classList.add('border-2', 'border-emerald-600');
            };
            reader.readAsDataURL(file);
        });

        let deletedImages = [];

        function deleteImage(id, btn) {
            deletedImages.push(id);
            document.getElementById('delete_images_input').value = JSON.stringify(deletedImages);
            btn.parentElement.remove();
        }

        function previewGallery(event) {
            const container = document.getElementById('gallery-container');
            const files = event.target.files;
            const addButton = event.target.parentElement;

            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.className = 'relative group aspect-square';
                    div.innerHTML = `
                            <img src="${e.target.result}" class="w-full h-full object-cover rounded-xl border border-gray-200">
                            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs opacity-100 transition-opacity shadow-lg">
                                <i data-lucide="x" class="w-3.5 h-3.5"></i>
                            </button>
                        `;
                    container.insertBefore(div, addButton);
                    lucide.createIcons();
                }
                reader.readAsDataURL(files[i]);
            }
        }
    </script>
@endsection