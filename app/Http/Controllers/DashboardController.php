<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('BACKEND_API_URL');

        $this->middleware(function ($request, $next) {
            $user = session('user');
            $token = session('api_token');

            if (!$user || !$token) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $routeName = $request->route()->getName();

            // Proteksi route Admin
            if (str_starts_with($routeName, 'admin.') && ($user['role'] ?? '') !== 'admin') {
                return redirect()->route('login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Admin.');
            }

            // Proteksi route Seller
            if (str_starts_with($routeName, 'seller.') && ($user['role'] ?? '') !== 'seller') {
                return redirect()->route('login')->with('error', 'Akses ditolak. Halaman ini hanya untuk Seller.');
            }

            return $next($request);
        });
    }

    public function adminIndex()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/statistics');
            $stats = $response->successful() ? $response->json() : [
                'total_users' => 0,
                'total_sellers' => 0,
                'total_products' => 0,
                'total_orders' => 0,
                'total_transactions' => 0,
                'total_revenue' => 0,
                'growth' => 0,
                'recent_transactions' => []
            ];
        } catch (\Exception $e) {
            $stats = [
                'total_users' => 0,
                'total_sellers' => 0,
                'total_products' => 0,
                'total_orders' => 0,
                'total_transactions' => 0,
                'total_revenue' => 0,
                'growth' => 0,
                'recent_transactions' => []
            ];
        }

        return view('admin.dashboard', $stats);
    }

    public function adminUsers(Request $request)
    {
        $token = session('api_token');
        $search = $request->query('search');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/users', [
                'search' => $search
            ]);
            $users = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $users = [];
        }
        return view('admin.users', compact('users'));
    }

    public function adminBuyers(Request $request)
    {
        $token = session('api_token');
        $search = $request->query('search');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/buyers', [
                'search' => $search
            ]);
            $users = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $users = [];
        }
        return view('admin.buyers', compact('users'));
    }

    public function adminUserShow($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . "/admin/users/{$id}");
            if (!$response->successful()) {
                return redirect()->route('admin.users')->with('error', 'User tidak ditemukan');
            }
            $user = $response->json();
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Terjadi kesalahan sistem');
        }
        return view('admin.users_show', compact('user'));
    }

    public function deleteUser($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->delete($this->apiBaseUrl . "/admin/users/{$id}");
            if ($response->successful()) {
                return back()->with('success', 'User berhasil dihapus');
            }
            return back()->with('error', 'Gagal menghapus user');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function adminSellers(Request $request)
    {
        $token = session('api_token');
        $search = $request->query('search');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/sellers', [
                'search' => $search
            ]);
            $sellers = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $sellers = [];
        }
        return view('admin.sellers', compact('sellers'));
    }

    public function adminProducts()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/products');
            $products = $response->successful() ? $response->json() : [];

            // Get stats too
            $statsResponse = Http::withToken($token)->get($this->apiBaseUrl . '/admin/statistics');
            $stats = $statsResponse->successful() ? $statsResponse->json() : [];
        } catch (\Exception $e) {
            $products = [];
            $stats = [];
        }
        return view('admin.products', compact('products', 'stats'));
    }

    public function adminProductShow($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/products/' . $id);
            $product = $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            $product = null;
        }

        if (!$product) {
            return redirect()->route('admin.products')->with('error', 'Produk tidak ditemukan');
        }

        return view('admin.products_show', compact('product'));
    }

    public function adminTransactions()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/orders');
            $orders = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $orders = [];
        }
        return view('admin.transactions', compact('orders'));
    }

    public function adminTransactionShow($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/orders/' . $id);
            $order = $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            $order = null;
        }

        if (!$order) {
            return redirect()->route('admin.transactions')->with('error', 'Transaksi tidak ditemukan');
        }

        return view('admin.transactions_show', compact('order'));
    }

    public function adminConfirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:success,failed'
        ]);

        $token = session('api_token');
        try {
            $response = Http::withToken($token)->post($this->apiBaseUrl . "/admin/orders/{$id}/confirm-payment", [
                'payment_status' => $request->payment_status
            ]);
            
            if ($response->successful()) {
                return back()->with('success', $response->json()['message'] ?? 'Konfirmasi pembayaran berhasil disimpan');
            }
            
            return back()->with('error', $response->json()['message'] ?? 'Gagal memproses konfirmasi pembayaran');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function adminDeleteProduct($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->delete($this->apiBaseUrl . "/admin/products/{$id}");
            if ($response->successful()) {
                return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus secara paksa');
            }
            return back()->with('error', 'Gagal menghapus produk');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }

    public function adminSettings()
    {
        return view('admin.settings');
    }
    public function adminChats()
    {
        return view('admin.chats');
    }

    // Seller Methods
    public function sellerIndex()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller/statistics');
            $stats = $response->successful() ? $response->json() : [
                'total_products' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'revenue' => 'Rp 0',
                'growth' => 0,
                'recent_orders' => [],
                'top_products' => [],
                'chart' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'data' => [0, 0, 0, 0, 0, 0]
                ]
            ];
        } catch (\Exception $e) {
            $stats = [
                'total_products' => 0,
                'pending_orders' => 0,
                'completed_orders' => 0,
                'revenue' => 'Rp 0',
                'growth' => 0,
                'recent_orders' => [],
                'top_products' => [],
                'chart' => [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    'data' => [0, 0, 0, 0, 0, 0]
                ]
            ];
        }

        return view('seller.dashboard', $stats);
    }

    public function sellerProducts()
    {
        $token = session('api_token');
        $search = request()->query('search');
        $category = request()->query('category');
        $status = request()->query('status');

        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller/products', [
                'search' => $search,
                'category' => $category,
                'status' => $status
            ]);

            // API backend mengembalikan array langsung, bukan di dalam key 'products'
            $products = $response->successful() ? $response->json() : [];

            // Ambil kategori unik untuk filter dari list produk
            $categories = collect($products)->pluck('category')->unique()->filter()->values()->all();

        } catch (\Exception $e) {
            $products = [];
            $categories = [];
        }

        return view('seller.products', compact('products', 'categories', 'search', 'category', 'status'));
    }

    public function sellerAddProduct()
    {
        $token = session('api_token');
        try {
            $response = Http::get($this->apiBaseUrl . '/categories');
            $categories = $response->successful() ? $response->json() : [];
        } catch (\Exception $e) {
            $categories = [];
        }
        return view('seller.add_product', compact('categories'));
    }

    public function sellerStoreProduct(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $token = session('api_token');
        $http = Http::withToken($token);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $http = $http->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            );
        }

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $file) {
                $http = $http->attach(
                    "additional_images[$index]",
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }
        }

        try {
            $response = $http->post($this->apiBaseUrl . '/products', [
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ]);

            if ($response->successful()) {
                return redirect()->route('seller.products')
                    ->with('success', 'Product berhasil ditambahkan');
            }

            return back()->with('error', $response->json()['message'] ?? 'Gagal menambahkan produk');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal');
        }
    }

    public function sellerEditProduct($id)
    {
        $token = session('api_token');

        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/products/' . $id);
            $product = $response->successful() ? $response->json() : null;

            // Ambil kategori juga
            $catResponse = Http::get($this->apiBaseUrl . '/categories');
            $categories = $catResponse->successful() ? $catResponse->json() : [];
        } catch (\Exception $e) {
            $product = null;
            $categories = [];
        }

        if (!$product) {
            return redirect()->route('seller.products')->with('error', 'Produk tidak ditemukan');
        }

        return view('seller.edit_product', compact('product', 'categories'));
    }

    public function sellerUpdateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'category' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048'
        ]);

        $token = session('api_token');
        $http = Http::withToken($token);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $http = $http->attach(
                'image',
                file_get_contents($image->getRealPath()),
                $image->getClientOriginalName()
            );
        }

        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $index => $file) {
                $http = $http->attach(
                    "additional_images[$index]",
                    file_get_contents($file->getRealPath()),
                    $file->getClientOriginalName()
                );
            }
        }

        try {
            $fields = [
                '_method' => 'PUT',
                'name' => $request->name,
                'category' => $request->category,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock
            ];

            if ($request->delete_images) {
                $fields['delete_images'] = $request->delete_images;
            }

            $response = $http->post($this->apiBaseUrl . '/products/' . $id, $fields);

            if ($response->successful()) {
                return redirect()->route('seller.products')
                    ->with('success', 'Produk berhasil diupdate');
            }

            return back()->with('error', $response->json()['message'] ?? 'Gagal update produk');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal');
        }
    }

    public function sellerDeleteProduct($id)
    {
        $token = session('api_token');

        try {
            $response = Http::withToken($token)->delete($this->apiBaseUrl . '/products/' . $id);

            if ($response->successful()) {
                return redirect()->route('seller.products')
                    ->with('success', 'Produk berhasil dihapus');
            }

            return back()->with('error', $response->json()['message'] ?? 'Gagal hapus produk');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal');
        }
    }
    public function sellerOrders(Request $request)
    {
        $token = session('api_token');
        $page = $request->query('page', 1);
        $search = $request->query('search');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller-orders', [
                'page' => $page,
                'search' => $search
            ]);
            $ordersData = $response->successful() ? $response->json() : null;

            if ($ordersData) {
                $mappedData = collect($ordersData['data'])->map(function ($order) {
                    return [
                        'id' => $order['id'],
                        'order_number' => '#' . str_pad($order['id'], 5, '0', STR_PAD_LEFT),
                        'buyer_name' => $order['buyer']['name'] ?? 'Pembeli Umum',
                        'products_count' => count($order['items'] ?? []),
                        'total_price' => 'Rp ' . number_format($order['final_price'] ?? 0, 0, ',', '.'),
                        'status' => $order['status'],
                        'date' => \Carbon\Carbon::parse($order['created_at'])->format('d M Y, H:i'),
                        'items' => collect($order['items'] ?? [])->map(function ($item) {
                            return [
                                'name' => $item['product']['name'] ?? 'Produk Dihapus',
                                'quantity' => $item['quantity']
                            ];
                        })->toArray()
                    ];
                })->toArray();

                // Manual pagination object for Blade
                $orders = new \Illuminate\Pagination\LengthAwarePaginator(
                    $mappedData,
                    $ordersData['total'],
                    $ordersData['per_page'],
                    $ordersData['current_page'],
                    ['path' => url()->current()]
                );
            } else {
                $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
            }
        } catch (\Exception $e) {
            $orders = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
        return view('seller.orders', compact('orders'));
    }
    public function sellerOrderShow($id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/orders/' . $id);
            $order = $response->successful() ? $response->json() : null;

            if (!$order) {
                return redirect()->route('seller.orders')->with('error', 'Pesanan tidak ditemukan');
            }

            return view('seller.order_detail', compact('order'));
        } catch (\Exception $e) {
            return redirect()->route('seller.orders')->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function sellerUpdateOrderStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,processed,shipped,completed'
        ]);

        $token = session('api_token');
        try {
            $response = Http::withToken($token)->put($this->apiBaseUrl . "/orders/{$id}/status", [
                'status' => $request->status
            ]);
            
            if ($response->successful()) {
                return back()->with('success', $response->json()['message'] ?? 'Status pesanan berhasil diperbarui');
            }
            
            return back()->with('error', $response->json()['message'] ?? 'Gagal memperbarui status pesanan');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan sistem');
        }
    }
    public function sellerChats()
    {
        return view('seller.chats');
    }
    public function sellerTransactions()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller/transactions');
            $data = $response->successful() ? $response->json() : [
                'stats' => ['total_revenue' => 'Rp 0', 'pending_revenue' => 'Rp 0', 'platform_fees' => 'Rp 0'],
                'transactions' => []
            ];
        } catch (\Exception $e) {
            $data = [
                'stats' => ['total_revenue' => 'Rp 0', 'pending_revenue' => 'Rp 0', 'platform_fees' => 'Rp 0'],
                'transactions' => []
            ];
        }
        return view('seller.transactions', $data);
    }
    public function sellerReviews()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller/reviews');
            $data = $response->successful() ? $response->json() : [
                'stats' => ['total' => 0, 'five_star' => 0, 'average' => 0],
                'reviews' => []
            ];
        } catch (\Exception $e) {
            $data = [
                'stats' => ['total' => 0, 'five_star' => 0, 'average' => 0],
                'reviews' => []
            ];
        }
        return view('seller.reviews', $data);
    }
    public function sellerSettings()
    {
        return view('seller.settings');
    }

    public function sellerReplyReview(Request $request, $id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->post($this->apiBaseUrl . "/seller/reviews/{$id}/reply", [
                'reply' => $request->reply
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Berhasil membalas ulasan');
            }

            return back()->with('error', $response->json()['message'] ?? 'Gagal membalas ulasan');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal');
        }
    }

    // --- WITHDRAWALS (SELLER) ---
    public function sellerWithdrawals()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/seller/withdrawals');
            $data = $response->json();
            $balance = $data['balance'] ?? 0;
            $withdrawals = $data['withdrawals'] ?? [];
            return view('seller.withdrawals', compact('balance', 'withdrawals'));
        } catch (\Exception $e) {
            return view('seller.withdrawals', ['balance' => 0, 'withdrawals' => []]);
        }
    }

    public function sellerStoreWithdrawal(Request $request)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->post($this->apiBaseUrl . '/seller/withdrawals', $request->all());
            if ($response->successful()) {
                return back()->with('success', 'Permintaan penarikan dana berhasil diajukan');
            }
            return back()->with('error', $response->json()['message'] ?? 'Gagal mengajukan penarikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal');
        }
    }

    // --- WITHDRAWALS (ADMIN) ---
    public function adminWithdrawals()
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/admin/withdrawals');
            $withdrawals = $response->json() ?? [];
            return view('admin.withdrawals', compact('withdrawals'));
        } catch (\Exception $e) {
            return view('admin.withdrawals', ['withdrawals' => []]);
        }
    }

    public function adminApproveWithdrawal(Request $request, $id)
    {
        $token = session('api_token');
        try {
            if ($request->hasFile('receipt_image')) {
                $file = $request->file('receipt_image');
                $response = Http::withToken($token)->attach(
                    'receipt_image', file_get_contents($file), $file->getClientOriginalName()
                )->post($this->apiBaseUrl . '/admin/withdrawals/' . $id . '/approve', [
                    'admin_note' => $request->admin_note
                ]);
            } else {
                return back()->with('error', 'Bukti transfer wajib diunggah');
            }
            
            if ($response->successful()) {
                return back()->with('success', 'Penarikan berhasil disetujui');
            }
            return back()->with('error', $response->json()['message'] ?? 'Gagal menyetujui penarikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal');
        }
    }

    public function adminRejectWithdrawal(Request $request, $id)
    {
        $token = session('api_token');
        try {
            $response = Http::withToken($token)->post($this->apiBaseUrl . '/admin/withdrawals/' . $id . '/reject', [
                'admin_note' => $request->admin_note
            ]);
            if ($response->successful()) {
                return back()->with('success', 'Penarikan ditolak');
            }
            return back()->with('error', $response->json()['message'] ?? 'Gagal menolak penarikan');
        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal');
        }
    }
}
