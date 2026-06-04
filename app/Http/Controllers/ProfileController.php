<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('BACKEND_API_URL');
    }

    /**
     * Bangun avatar URL lengkap berdasarkan data user dari API.
     */
    private function buildAvatarUrl(array $user): ?string
    {
        $backendUrl = env('BACKEND_URL', 'http://192.168.100.6:8001');
        $image = $user['profile_image'] ?? $user['avatar'] ?? null;

        if (!$image) {
            return null;
        }

        return str_starts_with($image, 'http')
            ? $image
            : $backendUrl . '/storage/' . $image;
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'nullable|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $token = Session::get('api_token');

        $payload = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $payload['password'] = $request->password;
            $payload['password_confirmation'] = $request->password_confirmation;
        }

        try {
            $response = Http::withToken($token)->put($this->apiBaseUrl . '/profile', $payload);

            if ($response->successful()) {
                $updatedUser = $response->json('user');

                // Pertahankan avatar_url agar foto profil tidak hilang setelah update
                $updatedUser['avatar_url'] = $this->buildAvatarUrl($updatedUser)
                    ?? Session::get('user.avatar_url');

                Session::put('user', $updatedUser);

                return back()->with('success', 'Profil berhasil diperbarui!');
            }

            return back()->with('error', 'Gagal memperbarui: ' . ($response->json()['message'] ?? 'Terjadi kesalahan.'));

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal.');
        }
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $token = Session::get('api_token');

        try {
            $response = Http::withToken($token)
                ->attach('avatar', file_get_contents($request->file('avatar')->getRealPath()), $request->file('avatar')->getClientOriginalName())
                ->post($this->apiBaseUrl . '/profile/avatar');

            if ($response->successful()) {
                $data = $response->json();
                $updatedUser = $data['user'];

                // Simpan full URL avatar agar bisa ditampilkan di Frontend
                $updatedUser['avatar_url'] = $this->buildAvatarUrl($updatedUser);

                Session::put('user', $updatedUser);

                return back()->with('success', 'Foto profil berhasil diperbarui!');
            }

            return back()->with('error', 'Gagal mengupload foto: ' . ($response->json()['message'] ?? 'Terjadi kesalahan.'));

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke API Backend gagal.');
        }
    }
}
