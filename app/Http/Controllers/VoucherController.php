<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class VoucherController extends Controller
{
    protected $apiBaseUrl;

    public function __construct()
    {
        $this->apiBaseUrl = env('BACKEND_API_URL');
    }

    public function index()
    {
        $token = Session::get('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/vouchers');

            // Log for debugging if empty
            if ($response->failed()) {
                \Log::error('Voucher API Failed: ' . $response->body());
            }

            $vouchers = $response->successful() && is_array($response->json()) ? $response->json() : [];
        } catch (\Exception $e) {
            \Log::error('Voucher Controller Error: ' . $e->getMessage());
            $vouchers = [];
        }

        return view('admin.vouchers', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers_create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'discount_percent' => 'required|numeric',
            'max_discount' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
            'quota' => 'required|numeric'
        ]);

        $token = Session::get('api_token');

        try {
            $response = Http::withToken($token)->post($this->apiBaseUrl . '/vouchers', $request->all());

            if ($response->successful()) {
                return redirect()->route('admin.vouchers')->with('success', 'Voucher berhasil dibuat!');
            }

            $message = $response->json('message') ?? 'Gagal membuat voucher (API Error).';
            return redirect()->route('admin.vouchers')->with('error', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.vouchers')->with('error', 'Koneksi ke API Backend gagal: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $token = Session::get('api_token');
        try {
            $response = Http::withToken($token)->get($this->apiBaseUrl . '/vouchers');
            if ($response->successful()) {
                $vouchers = $response->json();
                $voucher = collect($vouchers)->firstWhere('id', (int)$id);

                if ($voucher) {
                    return view('admin.vouchers_edit', compact('voucher'));
                }
            }
            return redirect()->route('admin.vouchers')->with('error', 'Voucher tidak ditemukan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.vouchers')->with('error', 'Koneksi ke API gagal.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required',
            'discount_percent' => 'required|numeric',
            'max_discount' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
            'quota' => 'required|numeric'
        ]);

        $token = Session::get('api_token');

        try {
            $response = Http::withToken($token)->put($this->apiBaseUrl . '/vouchers/' . $id, $request->all());

            if ($response->successful()) {
                return redirect()->route('admin.vouchers')->with('success', 'Voucher berhasil diperbarui!');
            }

            $message = $response->json('message') ?? 'Gagal memperbarui voucher.';
            return redirect()->route('admin.vouchers')->with('error', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.vouchers')->with('error', 'Koneksi API gagal: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $token = Session::get('api_token');

        try {
            $response = Http::withToken($token)->delete($this->apiBaseUrl . '/vouchers/' . $id);

            if ($response->successful()) {
                return redirect()->route('admin.vouchers')->with('success', 'Voucher berhasil dihapus!');
            }

            $message = $response->json('message') ?? 'Gagal menghapus voucher.';
            return redirect()->route('admin.vouchers')->with('error', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.vouchers')->with('error', 'Koneksi API gagal: ' . $e->getMessage());
        }
    }
}