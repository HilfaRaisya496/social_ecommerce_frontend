<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Social Commerce - Daftar Toko</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4 font-inter">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-emerald-900 mb-2 tracking-tight">ZEVEN<span
                    class="text-amber-500">.</span></h1>
            <p class="text-gray-500 font-medium">Be a Part of Our Elite Seller Community</p>
        </div>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Daftar Toko</h2>
            <p class="text-gray-500 mb-8 font-medium">Mulai perjalanan bisnis Anda bersama Zeven</p>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 italic">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input name="name" type="text" placeholder="Nama Lengkap Anda" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                    <input name="email" type="email" placeholder="email@contoh.com" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi</label>
                        <input name="password" type="password" placeholder="••••••••" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Kata Sandi</label>
                        <input name="password_confirmation" type="password" placeholder="••••••••" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 mt-2 rounded-xl text-white font-bold bg-gradient-to-r from-emerald-800 to-emerald-900 hover:from-emerald-900 hover:to-black transition-all active:scale-[0.98] shadow-xl shadow-emerald-100 flex items-center justify-center gap-2">
                    <span>Daftar Sekarang</span>
                    <i data-lucide="user-plus" class="w-5 h-5"></i>
                </button>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <p class="text-sm text-gray-500 font-inter">
                    Sudah punya akun penjual?
                    <a href="{{ route('login') }}"
                        class="text-amber-600 font-bold hover:text-amber-700 transition-colors">Masuk di
                        sini</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>