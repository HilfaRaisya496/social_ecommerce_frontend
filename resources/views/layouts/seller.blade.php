<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pusat Penjual - Social Commerce</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #059669;
            /* Emerald 600 */
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #047857;
            /* Emerald 700 */
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        window.apiToken = "{{ session('api_token') }}";
        window.apiBaseUrl = "{{ config('app.backend_api_url') }}";
        window.userId = "{{ session('user.id') }}";

        function sellerLayout() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                notifications: [],
                loading: false,

                async init() {
                    this.fetchNotifications();
                    // Polling notifications every 10 seconds for more real-time feel
                    setInterval(() => this.fetchNotifications(), 10000);
                },

                async contactSupport() {
                    if (!window.apiToken) return;
                    try {
                        this.loading = true;
                        console.log('Fetching admin ID from:', window.apiBaseUrl + '/chat/admin-id');
                        const response = await axios.get(window.apiBaseUrl + '/chat/admin-id', {
                            headers: {
                                'Authorization': 'Bearer ' + window.apiToken,
                                'Accept': 'application/json'
                            }
                        });

                        console.log('Admin ID Response:', response.data);
                        const adminId = response.data.admin_id;
                        if (adminId) {
                            window.location.href = "{{ route('seller.chats') }}?user_id=" + adminId;
                        } else {
                            Swal.fire({
                                icon: 'info',
                                title: 'Informasi',
                                text: 'Maaf, admin sedang sibuk atau tidak tersedia saat ini.',
                                confirmButtonColor: '#f97316'
                            });
                        }
                    } catch (error) {
                        console.error('Error contacting support:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal terhubung ke pusat bantuan. Pastikan koneksi internet Anda stabil.',
                            confirmButtonColor: '#f97316'
                        });
                    } finally {
                        this.loading = false;
                    }
                },

                async fetchNotifications() {
                    if (!window.apiToken) return;
                    try {
                        const response = await axios.get(window.apiBaseUrl + '/seller/notifications', {
                            headers: {
                                'Authorization': 'Bearer ' + window.apiToken,
                                'Accept': 'application/json'
                            }
                        });

                        console.log('Notifications received:', response.data); // Debug log
                        this.notifications = response.data.notifications || [];

                        // Re-initialize icons for dynamic content
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
                        await axios.post(window.apiBaseUrl + '/seller/notifications/' + id + '/read', {}, {
                            headers: { 'Authorization': 'Bearer ' + window.apiToken }
                        });
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                    }
                },

                async handleNotifClick(notif) {
                    // Mark as read first so it disappears from the list
                    await this.markAsRead(notif.id);

                    if (notif.type === 'chat') {
                        if (notif.sender_id) {
                            window.location.href = "{{ route('seller.chats') }}?user_id=" + notif.sender_id;
                        } else {
                            window.location.href = "{{ route('seller.chats') }}";
                        }
                    } else if (notif.type === 'order') {
                        const orderId = notif.id.split('_')[1];
                        if (orderId) {
                            window.location.href = "{{ route('seller.orders.show', ':id') }}".replace(':id', orderId);
                        } else {
                            window.location.href = "{{ route('seller.orders') }}";
                        }
                    } else if (notif.type === 'review') {
                        window.location.href = "{{ route('seller.reviews') }}";
                    } else if (notif.type === 'stock' || (notif.title && notif.title.includes('Stok'))) {
                        window.location.href = "{{ route('seller.products') }}";
                    } else if (notif.type === 'withdrawal') {
                        window.location.href = "{{ route('seller.withdrawals') }}";
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 text-gray-900">
    <div class="flex h-screen overflow-hidden" x-data="sellerLayout()" x-init="init()"
        @resize.window="sidebarOpen = window.innerWidth >= 1024 ? true : sidebarOpen">
        <!-- Sidebar - RESPONSIVE: Hidden di mobile, show di lg -->
        <aside
            class="fixed lg:relative w-64 h-full lg:h-auto bg-white border-r border-gray-200 flex flex-col flex-shrink-0 shadow-lg lg:shadow-sm z-40 lg:z-20 transition-all duration-300 left-0 top-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                        <i data-lucide="store" class="w-6 h-6 text-amber-400"></i>
                    </div>
                    <div>
                        <h2 class="font-bold text-gray-900">Zeven Seller</h2>
                        <p class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider">
                            {{ session('user.name') }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-4 overflow-y-auto">
                <ul class="space-y-1">
                    @php
                        $menuItems = [
                            ['route' => 'seller.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                            ['route' => 'seller.products', 'icon' => 'package', 'label' => 'Produk'],
                            ['route' => 'seller.orders', 'icon' => 'shopping-cart', 'label' => 'Pesanan'],
                            ['route' => 'seller.chats', 'icon' => 'message-square', 'label' => 'Chat'],
                            ['route' => 'seller.transactions', 'icon' => 'receipt', 'label' => 'Riwayat Transaksi'],
                            ['route' => 'seller.withdrawals', 'icon' => 'wallet', 'label' => 'Saldo & Penarikan'],
                            ['route' => 'seller.reviews', 'icon' => 'star', 'label' => 'Ulasan & Rating'],
                            ['route' => 'seller.settings', 'icon' => 'settings', 'label' => 'Pengaturan'],
                        ];
                    @endphp

                    @foreach($menuItems as $item)
                        <li>
                            <a href="{{ route($item['route']) }}"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs($item['route']) ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm shadow-emerald-50' : 'text-gray-600 hover:bg-gray-50' }}">
                                <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                                <span class="text-sm">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <!-- Card Pusat Bantuan di bagian bawah sidebar -->
            <div class="px-4 py-6 border-t border-gray-100">
                <div
                    class="bg-gradient-to-br from-emerald-700 to-emerald-900 rounded-2xl p-4 text-white relative overflow-hidden group shadow-lg shadow-emerald-100 italic">
                    <div class="relative z-10">
                        <h4 class="text-xs font-bold text-amber-400 opacity-80 uppercase tracking-widest mb-1">Pusat
                            Bantuan</h4>
                        <p class="text-[10px] opacity-90 leading-relaxed mb-3">Butuh bantuan? CS kami siap membantu
                            24/7.</p>
                        <button @click="contactSupport()"
                            class="w-full bg-amber-500 text-white hover:bg-amber-600 text-[10px] font-bold py-2 px-3 rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm border-none">
                            <i data-lucide="headphones" class="w-3 h-3 text-emerald-900"></i>
                            Hubungi Kami
                        </button>
                    </div>
                    <!-- Decorative background icon -->
                    <i data-lucide="help-circle"
                        class="absolute -right-4 -bottom-4 w-20 h-20 opacity-10 rotate-12 transition-transform group-hover:scale-110"></i>
                </div>
            </div>

            <div class="p-4 border-t border-gray-200">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all font-bold text-sm">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                        Keluar
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
                            @yield('header_subtitle', 'Kelola Bisnis & Monitoring Marketplace')</p>
                    </div>
                    <!-- Header Actions - RESPONSIVE -->
                    <div class="flex items-center gap-2 md:gap-4">
                        {{-- Notification Dropdown --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open; if(open) fetchNotifications();" @click.away="open = false"
                                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-xl transition-all shadow-sm hidden md:block">
                                <i data-lucide="bell" class="w-5 h-5"></i>
                                <template x-if="notifications && notifications.length > 0">
                                    <span class="absolute top-2 right-2 flex h-3 w-3">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                        <span
                                            class="relative inline-flex rounded-full h-3 w-3 bg-amber-500 border-2 border-white"></span>
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
                                    <h3 class="text-sm font-bold text-gray-900">Notifikasi Toko</h3>
                                    <span x-show="notifications.length > 0"
                                        class="text-[10px] bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold"
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
                                                            <span
                                                                class="flex h-2 w-2 rounded-full bg-purple-500"></span>
                                                        </template>
                                                    </div>
                                                    <p class="text-[11px] text-gray-500 leading-tight"
                                                        x-text="notif.message"></p>
                                                    <p class="text-[9px] text-amber-600 mt-2 font-medium"
                                                        x-text="notif.time"></p>
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

                        <a href="{{ route('seller.settings') }}"
                            class="flex items-center gap-2 md:gap-3 p-2 hover:bg-gray-100 rounded-xl transition-all border border-transparent hover:border-gray-200 shrink-0">
                            <div
                                class="w-9 h-9 rounded-xl overflow-hidden flex-shrink-0 flex items-center justify-center
                                {{ session('user.avatar_url') ? '' : 'bg-gradient-to-br from-orange-400 to-orange-500 shadow-lg shadow-orange-100' }}">
                                @if(session('user.avatar_url'))
                                    <img src="{{ session('user.avatar_url') }}" class="w-9 h-9 object-cover" alt="Avatar">
                                @else
                                    <i data-lucide="user" class="w-5 h-5 text-white"></i>
                                @endif
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-xs lg:text-sm font-bold text-gray-900">
                                    {{ session('user.name', 'Seller') }}
                                </p>
                                <p class="text-[10px] text-gray-500 capitalize">Seller</p>
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
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl px-6 py-3 font-bold' }
            });
        @endif
    </script>
    @stack('scripts')
</body>

</html>