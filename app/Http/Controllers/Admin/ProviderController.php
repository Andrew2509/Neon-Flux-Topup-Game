<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Http;

class ProviderController extends Controller
{
    public function index()
    {
        $providers = Provider::latest()->paginate(10);
        return view('admin.providers', compact('providers'));
    }

    public function create()
    {
        return view('admin.providers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider_id' => 'nullable|string|max:255',
            'api_key' => 'required|string|max:255',
            'mode' => 'nullable|string|in:sandbox,production',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|string|in:Aktif,Nonaktif,Error',
            'icon' => 'nullable|string|max:50',
        ]);

        Provider::create($request->all());

        return redirect()->route('admin.providers')->with('success', 'Provider berhasil ditambahkan.');
    }

    public function edit(Provider $provider)
    {
        return view('admin.providers.edit', compact('provider'));
    }

    public function update(Request $request, Provider $provider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'provider_id' => 'nullable|string|max:255',
            'api_key' => 'required|string|max:255',
            'mode' => 'nullable|string|in:sandbox,production',
            'balance' => 'required|numeric|min:0',
            'status' => 'required|string|in:Aktif,Nonaktif,Error',
            'icon' => 'nullable|string|max:50',
        ]);

        $provider->update($request->all());

        return redirect()->route('admin.providers')->with('success', 'Provider berhasil diperbarui.');
    }

    public function destroy(Provider $provider)
    {
        $provider->delete();
        return redirect()->route('admin.providers')->with('success', 'Provider berhasil dihapus.');
    }

    public function syncBalance(Provider $provider)
    {
        $name = strtolower($provider->name);

        // Cek TokoVoucher
        if (str_contains($name, 'toko') || str_contains($name, 'digiflazz')) {
            $memberCode = $provider->provider_id;
            $secret = $provider->api_key;
            $signature = md5($memberCode . ":" . $secret);

            try {
                $response = Http::get("https://api.tokovoucher.net/member", [
                    'member_code' => $memberCode,
                    'signature' => $signature,
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    if (isset($result['data']['saldo'])) {
                        $saldo = $result['data']['saldo'];
                        $provider->update(['balance' => $saldo, 'status' => 'Aktif']);
                        return back()->with('success', 'Saldo TokoVoucher berhasil disinkronisasi: Rp ' . number_format($saldo, 0, ',', '.'));
                    }
                }
                return back()->with('error', 'Gagal memuat saldo: Cek pengaturan Member Code & Secret Key TokoVoucher.');
            } catch (\Exception $e) {
                return back()->with('error', 'Koneksi ke server TokoVoucher terputus.');
            }
        }

        // Cek Duitku
        elseif (str_contains($name, 'duitku')) {
            return back()->with('info', 'Saldo Duitku (Payment Gateway) merupakan funds mutasi harian, silakan cek langsung di Dashboard Duitku.');
        }

        // Cek Midtrans
        elseif (str_contains($name, 'midtrans')) {
            return back()->with('info', 'Midtrans adalah Payment Gateway. Saldo Anda akan tercatat sebagai settlement yang masuk ke rekening bank Anda sesuai jadwal disbursement Midtrans. Silakan cek detail di Dashboard Midtrans.');
        }

        // Cek Orbit WhatsApp
        elseif (str_contains($name, 'whatsapp') || str_contains($name, 'orbit')) {
            try {
                $response = Http::withToken($provider->api_key)
                    ->get("https://orbit-whatsapp-api.vercel.app/api/v1/devices");

                if ($response->successful()) {
                    $result = $response->json();
                    if ($result['success'] && !empty($result['data'])) {
                        $device = $result['data'][0]; // Assume first device
                        $status = ($device['status'] === 'connected') ? 'Aktif' : 'Error';
                        $provider->update(['status' => $status]);
                        
                        $msg = "Status WhatsApp (" . $device['phone_number'] . "): " . $device['status'];
                        return back()->with('success', $msg);
                    }
                }
                return back()->with('error', 'Gagal memuat status WhatsApp: Cek API Key Orbit.');
            } catch (\Exception $e) {
                return back()->with('error', 'Koneksi ke server Orbit gagal.');
            }
        }

        // Provider Lainnya
        return back()->with('info', 'Provider ini tidak mendukung sinkronisasi saldo otomatis.');
    }

    public function showDepositForm(Provider $provider)
    {
        $name = strtolower($provider->name);
        if (!str_contains($name, 'toko') && !str_contains($name, 'digiflazz')) {
            return back()->with('error', 'Fitur topup deposit saat ini hanya didukung untuk Provider TokoVoucher.');
        }

        return view('admin.providers.deposit', compact('provider'));
    }

    public function processDeposit(Request $request, Provider $provider)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:10000',
            'metode' => 'required|string',
        ]);

        $memberCode = $provider->provider_id;
        $secret = $provider->api_key;

        try {
            $response = Http::get("https://api.tokovoucher.net/v1/deposit", [
                'member_code' => $memberCode,
                'secret' => $secret,
                'nominal' => $request->nominal,
                'kode' => $request->metode,
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == 1) {
                $data = $result['data'];
                $isUrl = str_starts_with($data['pay'], 'http');

                $msg = "<b>Deposit Berhasil Dibuat!</b><br>";
                $msg .= "Silakan bayar sebesar <b>Rp " . number_format($data['total_transfer'] ?? $data['nominal'], 0, ',', '.') . "</b> menggunakan <b>" . $data['metode'] . "</b>.<br>";

                if ($isUrl) {
                    $msg .= "<div class='mt-2'><img src='".$data['pay']."' class='w-48 h-48 rounded-lg border border-white/10 mx-auto' alt='QR Code Payment'></div>";
                    $msg .= "<p class='text-center mt-1'>Scan QR di atas untuk membayar</p>";
                } else {
                    $msg .= "<b>Tujuan/Nomor Bayar:</b> <code class='bg-white/10 px-2 py-1 rounded'>" . $data['pay'] . "</code><br>";
                    if (isset($data['pay_name']) && $data['pay_name']) {
                        $msg .= "<b>Atas Nama:</b> " . $data['pay_name'];
                    }
                }

                return redirect()->route('admin.providers')->with('success', $msg);
            } else {
                $errorMsg = $result['error_msg'] ?? 'Terjadi kesalahan saat membuat tiket deposit.';
                return back()->with('error', 'Gagal: ' . $errorMsg);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke server TokoVoucher gagal: ' . $e->getMessage());
        }
    }
}
