<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Social Commerce - Masuk</title>
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

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-extrabold text-emerald-900 mb-2 tracking-tight">ZEVEN<span
                    class="text-amber-500">.</span></h1>
            <p class="text-gray-500 font-medium">Elevating Your Marketplace Experience</p>
        </div>

        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Masuk</h2>
            <p class="text-gray-500 mb-8 font-medium">Gunakan akses akun Seller atau Admin Anda</p>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 p-3 bg-green-50 text-green-600 rounded-xl text-sm border border-green-100">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Email</label>
                    <input name="email" type="email" placeholder="email@contoh.com" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi</label>
                    <input name="password" type="password" placeholder="••••••••" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition-all font-inter">
                </div>

                <button type="submit"
                    class="w-full py-4 mt-2 rounded-xl text-white font-bold bg-gradient-to-r from-emerald-800 to-emerald-900 hover:from-emerald-900 hover:to-black transition-all active:scale-[0.98] shadow-xl shadow-emerald-100 flex items-center justify-center gap-2">
                    <span>Masuk Sekarang</span>
                    <i data-lucide="log-in" class="w-5 h-5"></i>
                </button>

                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-100"></div>
                    </div>
                    <div class="relative flex justify-center text-xs uppercase">
                        <span class="bg-white px-3 text-gray-400 font-medium tracking-widest">Atau</span>
                    </div>
                </div>

                <a href="{{ route('google.login') }}"
                    class="w-full py-3.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-bold hover:bg-gray-50 hover:border-gray-300 transition-all active:scale-[0.98] flex items-center justify-center gap-3">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-5 h-5">
                    <span>Lanjutkan dengan Google</span>
                </a>
            </form>

            <div class="mt-8 text-center pt-6 border-t border-gray-100">
                <p class="text-sm text-gray-500">
                    Belum punya toko?
                    <a href="{{ route('register') }}"
                        class="text-amber-600 font-bold hover:text-amber-700 transition-colors">Daftar Toko
                        Baru</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>