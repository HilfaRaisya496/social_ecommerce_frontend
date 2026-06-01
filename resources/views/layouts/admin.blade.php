<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel - Social Commerce</title>
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.apiToken = "{{ session('api_token') }}";
        window.apiBaseUrl = "{{ config('app.backend_api_url') }}";
        window.userId = "{{ session('user.id') }}";

        function adminLayout() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                notifications: [],
                loading: false,

                init() {
                    this.fetchNotifications();
                    setInterval(() => this.fetchNotifications(), 10000);
                },

                async fetchNotifications() {
                    if (!window.apiToken) return;
                    try {
                        const response = await axios.get(window.apiBaseUrl + '/admin/notifications', {
                            headers: {
                                'Authorization': 'Bearer ' + window.apiToken,
                                'Accept': 'application/json'
                            }
                        });
                        this.notifications = response.data.notifications || [];
                        this.$nextTick(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        });
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    }
                },

                async markAsRead(id) {
                    try {
                        await axios.post(window.apiBaseUrl + '/admin/notifications/' + id + '/read', {}, {
                            headers: { 'Authorization': 'Bearer ' + window.apiToken }
                        });
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                },

                async handleNotifClick(notif) {
                    await this.markAsRead(notif.id);

                    if (notif.type === 'chat') {
                        window.location.href = "{{ route('admin.chats') }}";
                    } else if (notif.type === 'user') {
                        window.location.href = "{{ route('admin.users') }}";
                    } else if (notif.type === 'stock') {
                        window.location.href = "{{ route('admin.products') }}";
                    } else if (notif.type === 'payment') {
                        const orderId = notif.id.split('_')[1];
                        if (orderId) {
                            window.location.href = "{{ route('admin.transactions.show', ':id') }}".replace(':id', orderId);
                        } else {
                            window.location.href = "{{ route('admin.transactions') }}";
                        }
                    } else if (notif.type === 'withdrawal') {
                        window.location.href = "{{ route('admin.withdrawals') }}";
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="flex h-screen overflow-hidden" x-data="adminLayout()" x-init="init()"
        @resize.window="sidebarOpen = window.innerWidth >= 1024 ? true : sidebarOpen">
        <!-- Sidebar - RESPONSIVE: Hidden di mobile, show di lg -->
        <aside
            class="fixed lg:relative w-64 h-full lg:h-auto bg-white border-r border-gray-200 flex flex-col flex-shrink-0 shadow-lg lg:shadow-sm z-40 lg:z-20 transition-all duration-300 left-0 top-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="p-4 lg:p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg shadow-amber-100">
                        <i data-lucide="shield-check" class="w-6 h-6 text-emerald-900"></i>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-bold text-gray-900 text-sm lg:text-base">Zeven Admin</h2>
                        <p class="text-[9px] lg:text-[10px] text-amber-600 font-bold uppercase tracking-wider truncate">
                            Pusat Kontrol</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-3 lg:p-4 overflow-y-auto custom-scrollbar">
                <ul class="space-y-1">
                    @php
                        $menuItems = [
                            ['route' => 'admin.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                            ['route' => 'admin.users', 'icon' => 'users', 'label' => 'User'],
                            ['route' => 'admin.buyers', 'icon' => 'user-check', 'label' => 'Pembeli'],
                            ['route' => 'admin.sellers', 'icon' => 'store', 'label' => 'Penjual'],
                            ['route' => 'admin.chats', 'icon' => 'message-square', 'label' => 'Chat'],
                            ['route' => 'admin.products', 'icon' => 'package', 'label' => 'Produk'],
                            ['route' => 'admin.transactions', 'icon' => 'receipt', 'label' => 'Transaksi'],
                            ['route' => 'admin.withdrawals', 'icon' => 'banknote', 'label' => 'Pencairan Dana'],
                            ['route' => 'admin.vouchers', 'icon' => 'ticket', 'label' => 'Voucher'],
                            ['route' => 'admin.settings', 'icon' => 'settings', 'label' => 'Pengaturan'],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-3 lg:px-4 py-3 rounded-xl transition-all text-xs lg:text-sm {{ request()->routeIs($item['route']) ? 'bg-amber-50 text-amber-700 font-bold shadow-sm shadow-amber-50' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5 shrink-0 text-emerald-700"></i>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="p-3 lg:p-4 border-t border-gray-200">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 lg:px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all font-medium text-xs lg:text-sm">
                        <i data-lucide="log-out" class="w-5 h-5 shrink-0"></i>
                        <span class="truncate">Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Mobile Sidebar Overlay -->
        <div class="fixed inset-0 bg-black/50 lg:hidden z-30 transition-opacity"
            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'" @click="sidebarOpen = false"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header - RESPONSIVE: Smaller padding on mobile -->
            <header class="bg-white border-b border-gray-200 px-4 md:px-6 lg:px-8 py-3 md:py-4 z-10">
                <div class="flex items-center justify-between gap-4">
                    <!-- Hamburger Button (Mobile Only) -->
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 hover:bg-gray-100 rounded-xl transition-all">
                        <i data-lucide="menu" class="w-5 h-5 text-gray-600"></i>
                    </button>

                    <!-- Header Title - RESPONSIVE -->
                    <div class="flex-1 min-w-0">
                        <h1 class="text-lg md:text-xl font-bold text-gray-900 truncate">
                            @yield('header_title', 'Dashboard')
                        </h1>
                        <p class="text-xs md:text-sm text-gray-500 truncate">
                            @yield('header_subtitle', 'Sistem Administrasi & Monitoring Marketplace')</p>
                    </div>
                    <!-- Header Actions - RESPONSIVE -->
                    <div class="flex items-center gap-2 md:gap-4">
                        {{-- Notification Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open; if(open) fetchNotifications();" @click.away="open = false"
                                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all hidden md:block">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <template x-if="notifications && notifications.length > 0">
                                    <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                        <span
                                            class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 border-2 border-white"></span>
                                    </span>
                                </template>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                class="absolute right-0 mt-4 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden"
                                style="display: none;">
                                <div
                                    class="p-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                                    <h3 class="text-sm font-bold text-gray-900">Pusat Notifikasi</h3>
                                    <span x-show="notifications.length > 0"
                                        class="text-[10px] bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-bold"
                                        x-text="notifications.length + ' Baru'"></span>
                                </div>
                                <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                                    <template x-for="notif in notifications" :key="notif.id">
                                        <div class="p-4 hover:bg-gray-50 transition-all border-b border-gray-50 cursor-pointer group"
                                            @click="handleNotifClick(notif)">
                                            <div class="flex gap-3">
                                                <div :class="notif.bg_color + ' ' + notif.text_color"
                                                    class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 transition-transform group-hover:scale-110">
                                                    <i :data-lucide="notif.icon" class="w-5 h-5"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex justify-between items-start">
                                                        <p class="text-xs font-bold text-gray-900 leading-none mb-1"
                                                            x-text="notif.title"></p>
                                                        <template x-if="notif.type === 'chat'">
                                                            <span class="flex h-2 w-2 rounded-full bg-amber-500"></span>
                                                        </template>
                                                    </div>
                                                    <p class="text-[11px] text-gray-500 leading-tight"
                                                        x-text="notif.message"></p>
                                                    <p class="text-[9px] text-amber-500 mt-2 font-medium"
                                                        x-text="notif.time_human"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="notifications && notifications.length === 0">
                                        <div class="p-8 text-center bg-white">
                                            <div
                                                class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                                <i data-lucide="bell-off" class="w-8 h-8 text-gray-300"></i>
                                            </div>
                                            <p class="text-sm text-gray-500">Tidak ada notifikasi baru</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.settings') }}"
                            class="flex items-center gap-2 md:gap-3 p-2 hover:bg-gray-100 rounded-xl transition-all shrink-0">
                            <div
                                class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center
                                {{ session('user.avatar_url') ? '' : 'bg-gradient-to-br from-amber-500 to-amber-600' }}">
                                @if(session('user.avatar_url'))
                                    <img src="{{ session('user.avatar_url') }}" class="w-9 h-9 object-cover" alt="Avatar">
                                @else
                                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                @endif
                            </div>
                            <div class="text-left hidden md:block">
                                <p class="text-xs lg:text-sm font-medium text-gray-900">
                                    {{ session('user.name', 'Admin') }}
                                </p>
                                <p class="text-[10px] text-gray-500 capitalize">
                                    {{ session('user.role', 'Administrator') }}
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Main Content - RESPONSIVE: Padding smaller on mobile -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        // Setup Axios
        axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.apiToken;
        axios.defaults.headers.common['Accept'] = 'application/json';

        lucide.createIcons();

        // Global Alert Handler
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000,
                customClass: { popup: 'rounded-2xl' }
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-2.5' }
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>