@extends('layouts.seller')

@section('header_title', 'Pengaturan Profil')

@section('content')
    <div class="space-y-4">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-center gap-3 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100">
                <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-center gap-3 p-4 bg-red-50 text-red-600 rounded-xl border border-red-100">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
        @endif

        {{-- Satu Card: Profil Lengkap --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">

            {{-- Header Card --}}
            <div class="flex items-center gap-3 p-6 border-b border-gray-100">
                <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Informasi Profil</h3>
                    <p class="text-sm text-gray-500">Perbarui foto, nama, email, dan kata sandi Anda</p>
                </div>
            </div>

            <div class="p-6 space-y-6">

                {{-- Bagian Foto Profil --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Foto Profil</label>
                    <div class="flex items-center gap-5">
                        {{-- Avatar Preview --}}
                        <div class="flex-shrink-0">
                            @if(session('user.avatar_url'))
                                <img id="avatarPreview" src="{{ session('user.avatar_url') }}"
                                    class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-600 shadow-md"
                                    alt="Avatar">
                            @else
                                <div id="avatarInitial"
                                    class="w-20 h-20 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-emerald-100">
                                    {{ strtoupper(substr(session('user.name', 'S'), 0, 1)) }}
                                </div>
                                <img id="avatarPreview" src=""
                                    class="w-20 h-20 rounded-2xl object-cover border-2 border-emerald-600 shadow-md hidden"
                                    alt="Avatar">
                            @endif
                        </div>

                        {{-- Upload Form --}}
                        <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST"
                            enctype="multipart/form-data" class="flex-1 flex items-center gap-3">
                            @csrf
                            <input id="avatarInput" name="avatar" type="file" accept="image/*"
                                class="flex-1 text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer" />
                            <button id="uploadBtn" type="submit" disabled
                                class="flex-shrink-0 px-4 py-2 rounded-xl font-medium text-sm transition-all bg-gray-200 text-gray-400 cursor-not-allowed">
                                Unggah
                            </button>
                        </form>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Format JPG, PNG, WEBP. Maks 2MB.</p>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Form Nama, Email & Password --}}
                <form id="profileForm" action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap / Nama Toko</label>
                        <input type="text" name="name" id="inputName" value="{{ old('name', session('user.name', '')) }}"
                            data-original="{{ session('user.name', '') }}" placeholder="Nama Anda"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                        <p class="text-xs text-gray-400 mt-1">Nama ini juga tampil sebagai nama toko di marketplace.</p>
                        @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                        <input type="email" name="email" id="inputEmail"
                            value="{{ old('email', session('user.email', '')) }}"
                            data-original="{{ session('user.email', '') }}" placeholder="email@contoh.com"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                        @error('email')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                        <input type="tel" name="phone" id="inputPhone" value="{{ old('phone', session('user.phone', '')) }}"
                            data-original="{{ session('user.phone', '') }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                        @error('phone')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                        <textarea name="address" id="inputAddress" rows="3"
                            data-original="{{ session('user.address', '') }}" placeholder="Jl. Contoh No. 1, Kota..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all resize-none">{{ old('address', session('user.address', '')) }}</textarea>
                        @error('address')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-sm font-medium text-gray-700 mb-3">Ganti Kata Sandi
                            <span class="text-gray-400 font-normal">(kosongkan jika tidak ingin mengganti)</span>
                        </p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm text-gray-600 mb-1.5">Kata Sandi Baru</label>
                                <input type="password" name="password" id="inputPassword" placeholder="Minimal 6 karakter"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                                @error('password')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1.5">Konfirmasi Kata Sandi</label>
                                <input type="password" name="password_confirmation" id="inputPasswordConfirm"
                                    placeholder="Ulangi kata sandi baru"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all" />
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Simpan --}}
                    <div class="flex justify-end pt-2">
                        <button id="saveBtn" type="submit" disabled
                            class="px-6 py-2.5 rounded-xl font-medium text-sm transition-all bg-gray-200 text-gray-400 cursor-not-allowed">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

    <script>
        // ===== Upload Button: aktif saat file dipilih =====
        const avatarInput = document.getElementById('avatarInput');
        const uploadBtn = document.getElementById('uploadBtn');

        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                // Preview gambar yang dipilih
                const reader = new FileReader();
                reader.onload = (e) => {
                    const preview = document.getElementById('avatarPreview');
                    const initial = document.getElementById('avatarInitial');
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (initial) initial.classList.add('hidden');
                };
                reader.readAsDataURL(file);

                // Aktifkan tombol Upload
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                uploadBtn.classList.add('bg-gray-900', 'hover:bg-gray-800', 'text-white', 'active:scale-95', 'cursor-pointer');
            } else {
                uploadBtn.disabled = true;
                uploadBtn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                uploadBtn.classList.remove('bg-gray-900', 'hover:bg-gray-800', 'text-white', 'active:scale-95', 'cursor-pointer');
            }
        });

        // ===== Save Button: aktif saat ada perubahan di form =====
        const saveBtn = document.getElementById('saveBtn');
        const watchedInputs = [
            document.getElementById('inputName'),
            document.getElementById('inputEmail'),
            document.getElementById('inputPhone'),
            document.getElementById('inputAddress'),
            document.getElementById('inputPassword'),
            document.getElementById('inputPasswordConfirm'),
        ];

        function checkFormChanged() {
            const nameChanged = document.getElementById('inputName').value !== document.getElementById('inputName').dataset.original;
            const emailChanged = document.getElementById('inputEmail').value !== document.getElementById('inputEmail').dataset.original;
            const phoneChanged = document.getElementById('inputPhone').value !== (document.getElementById('inputPhone').dataset.original || "");
            const addressChanged = document.getElementById('inputAddress').value !== (document.getElementById('inputAddress').dataset.original || "");
            const passChanged = document.getElementById('inputPassword').value.length > 0;
            const confirmChanged = document.getElementById('inputPasswordConfirm').value.length > 0;

            const hasChange = nameChanged || emailChanged || phoneChanged || addressChanged || passChanged || confirmChanged;

            saveBtn.disabled = !hasChange;
            if (hasChange) {
                saveBtn.classList.remove('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                saveBtn.classList.add('bg-emerald-700', 'hover:bg-emerald-800', 'text-white', 'shadow-lg', 'shadow-emerald-100', 'active:scale-95', 'cursor-pointer');
            } else {
                saveBtn.classList.add('bg-gray-200', 'text-gray-400', 'cursor-not-allowed');
                saveBtn.classList.remove('bg-emerald-700', 'hover:bg-emerald-800', 'text-white', 'shadow-lg', 'shadow-emerald-100', 'active:scale-95', 'cursor-pointer');
            }
        }

        watchedInputs.forEach(input => {
            if (input) input.addEventListener('input', checkFormChanged);
        });
    </script>
@endsection