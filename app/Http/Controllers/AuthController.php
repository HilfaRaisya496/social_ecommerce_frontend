<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('BACKEND_API_URL');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            // Tembak API Login di Backend
            $response = Http::post($this->apiBaseUrl . '/login', [
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user = $data['user'];

                // Konstruksi URL Avatar agar terbaca di Frontend (Cek agar tidak dobel URL)
                if (!empty($user['profile_image'])) {
                    if (str_starts_with($user['profile_image'], 'http')) {
                        $user['avatar_url'] = $user['profile_image'];
                    } else {
                        $backendUrl = env('BACKEND_URL', 'http://192.168.100.6:8001');
                        $user['avatar_url'] = $backendUrl . '/storage/' . $user['profile_image'];
                    }
                } else if (!empty($user['avatar'])) {
                    // Fallback to 'avatar' field if profile_image is empty
                    if (str_starts_with($user['avatar'], 'http')) {
                        $user['avatar_url'] = $user['avatar'];
                    } else {
                        $backendUrl = env('BACKEND_URL', 'http://192.168.100.6:8001');
                        $user['avatar_url'] = $backendUrl . '/storage/' . $user['avatar'];
                    }
                }

                // Simpan Token dan Data User di Session Frontend
                Session::put('api_token', $data['token']);
                Session::put('user', $user);

                // Redirect sesuai Role
                if ($user['role'] === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user['role'] === 'seller') {
                    return redirect()->route('seller.dashboard');
                }

                return redirect('/')->with('error', 'Role tidak memiliki akses dashboard.');
            }

            return back()->with('error', 'Login gagal: ' . ($response->json()['message'] ?? 'Kesalahan tidak diketahui'));

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal. Pastikan Backend di port 8001 sudah berjalan.');
        }
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function registerSeller(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed', // Pastikan ada password_confirmation
        ]);

        try {
            $response = Http::post($this->apiBaseUrl . '/register-seller', [
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password,
            ]);

            if ($response->successful()) {
                return redirect()->route('login')->with('success', 'Registrasi Toko berhasil! Silakan login.');
            }

            return back()->with('error', 'Registrasi gagal: ' . ($response->json()['message'] ?? 'Email mungkin sudah terdaftar.'));

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal.');
        }
    }

    public function logout()
    {
        $token = Session::get('api_token');

        if ($token) {
            // Tembak API Logout di Backend (Opsional, agar token hangus di DB)
            Http::withToken($token)->post($this->apiBaseUrl . '/logout');
        }

        // Hapus Session
        Session::forget(['api_token', 'user']);

        return redirect()->route('login')->with('success', 'Berhasil logout');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Kirim data Google User ke Backend API untuk login/register
            $response = Http::post($this->apiBaseUrl . '/login/google', [
                'email' => $googleUser->getEmail(),
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $user = $data['user'];

                // Konstruksi URL Avatar agar terbaca di Frontend
                if (!empty($user['profile_image'])) {
                    if (str_starts_with($user['profile_image'], 'http')) {
                        $user['avatar_url'] = $user['profile_image'];
                    } else {
                        $backendUrl = env('BACKEND_URL', 'http://192.168.100.6:8001');
                        $user['avatar_url'] = $backendUrl . '/storage/' . $user['profile_image'];
                    }
                } else if (!empty($user['avatar'])) {
                    if (str_starts_with($user['avatar'], 'http')) {
                        $user['avatar_url'] = $user['avatar'];
                    } else {
                        $backendUrl = env('BACKEND_URL', 'http://192.168.100.6:8001');
                        $user['avatar_url'] = $backendUrl . '/storage/' . $user['avatar'];
                    }
                }

                Session::put('api_token', $data['token']);
                Session::put('user', $user);

                if ($user['role'] === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($user['role'] === 'seller') {
                    return redirect()->route('seller.dashboard');
                }

                return redirect('/')->with('error', 'Role tidak memiliki akses dashboard.');
            }

            return redirect()->route('login')->with('error', 'Login Google gagal di sistem.');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Terjadi kesalahan saat login dengan Google.');
        }
    }
}
