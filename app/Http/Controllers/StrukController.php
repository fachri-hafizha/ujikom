<?php

namespace App\Http\Controllers;

use App\Models\Pemenang;
use App\Models\Struk;  // Pastikan import ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;  // Untuk logging error
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Transaction;

class StrukController extends Controller
{
    // Hapus MidtransService jika tidak digunakan; jika ya, gunakan di sini
    // protected $midtrans;
    // public function __construct(MidtransService $midtrans) {
    //     $this->midtrans = $midtrans;
    // }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $struk = Struk::where('user_id', Auth::user()->id)->latest()->firstOrFail();
        return view('struk', compact('struk'));
    }

    /**
     * Check status transaksi (untuk manual check).
     */
    public function checkStatus(string $kode)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        $struk = Struk::where('kode_struk', $kode)->firstOrFail();
        $order_id = $struk->kode_struk;

        try {
            $status = Transaction::status($order_id);

            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                $struk->status = 'berhasil';
                $struk->save();
                toast('Pembayaran berhasil dikonfirmasi!', 'success');
            } elseif ($status->transaction_status == 'pending') {
                toast('Pembayaran masih pending.', 'info');
            } else {
                $struk->status = 'gagal';
                $struk->save();
                toast('Pembayaran gagal/expired.', 'error');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Midtrans checkStatus error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal cek status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Tampilkan struk dan generate snap token jika perlu.
     */
    public function struk(string $kodestruk)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);

        $struk = Struk::where('kode_struk', $kodestruk)->firstOrFail();
        $pemenang = Pemenang::findOrFail($struk->id_pemenang);

        // Hitung total
        $bidakhir = $pemenang->bid + $struk->lelang->barang->harga;
        $adminfee = $bidakhir * 0.05;
        $total = $adminfee + $bidakhir;

        // Generate Snap Token jika belum ada atau expired (opsional, tapi baik untuk refresh)
        if (!$struk->snap_token || $this->isTokenExpired($struk)) {
            $params = [
                'transaction_details' => [
                    'order_id' => $struk->kode_struk,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $pemenang->user->nama_lengkap,
                    'email' => $pemenang->user->email,
                ],
                'item_details' => [  // Tambahkan detail item untuk kejelasan
                    [
                        'id' => $struk->lelang->barang->id,
                        'price' => $bidakhir,
                        'quantity' => 1,
                        'name' => $struk->lelang->barang->nama_barang,
                    ],
                    [
                        'id' => 'admin_fee',
                        'price' => $adminfee,
                        'quantity' => 1,
                        'name' => 'Biaya Admin 5%',
                    ],
                ],
            ];

            try {
                $snapToken = Snap::getSnapToken($params);
                $struk->snap_token = $snapToken;
                $struk->snap_token_created_at = now();  // Tambahkan kolom ini di tabel (lihat migration di bawah)
                $struk->save();
            } catch (\Exception $e) {
                Log::error('Midtrans generate token error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal generate token pembayaran: ' . $e->getMessage());
            }
        } else {
            $snapToken = $struk->snap_token;
        }

        return view('struk', compact('struk', 'pemenang', 'snapToken'));
    }

    /**
     * Webhook handler untuk callback Midtrans (penting untuk update otomatis).
     * Route: POST /midtrans/webhook
     */
    public function webhook(Request $request)
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new \Midtrans\Notification();

            $transaction = $notification->transaction_status;
            $order_id = $notification->order_id;
            $fraud = $notification->fraud_status;

            $struk = Struk::where('kode_struk', $order_id)->first();
            if (!$struk) {
                Log::error('Struk not found for order_id: ' . $order_id);
                return response('Struk not found', 404);
            }

            if ($transaction == 'capture' || $transaction == 'settlement') {
                if ($fraud == 'challenge') {
                    $struk->status = 'challenge';
                } else if ($fraud == 'accept') {
                    $struk->status = 'berhasil';
                }
            } else if ($transaction == 'cancel' || $transaction == 'deny' || $transaction == 'expire') {
                $struk->status = 'gagal';
            } else if ($transaction == 'pending') {
                $struk->status = 'pending';
            }

            $struk->save();
            Log::info('Webhook processed for order_id: ' . $order_id . ' - Status: ' . $struk->status);

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response('Error', 500);
        }
    }

    /**
     * Helper: Cek apakah snap token expired (opsional, tapi Midtrans token ~24 jam).
     */
    private function isTokenExpired(Struk $struk)
    {
        if (!$struk->snap_token_created_at) return true;
        return now()->diffInHours($struk->snap_token_created_at) > 23;  // Refresh jika >23 jam
    }
}