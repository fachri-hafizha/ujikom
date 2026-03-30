<?php

namespace App\Http\Controllers;

use App\Models\Pemenang;
use App\Services\MidtransService;
use App\Models\Struk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Transaction;

class StrukController extends Controller
{
    protected $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $struk = Struk::where('user_id', Auth::user()->id)->latest()->firstOrFail();
        return view('struk', compact('struk'));
    }

    public function checkStatus(string $kode)
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$clientKey = config('services.midtrans.client_key');
       Config::$isProduction = false;
       Config::$isSanitized = true;
       Config::$is3ds = true;

        // Ambil struk berdasarkan kode
        $struk = Struk::where('kode_struk', $kode)->firstOrFail();
        $order_id = $struk->kode_struk;
 

        try {
            // Memeriksa status transaksi
            $status = Transaction::status($order_id);

            // Memperbarui status struk berdasarkan status transaksi
            if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                $struk->status = 'berhasil';
                $struk->save();
                toast('Pembayaran berhasil dikonfirmasi!', 'success');
            } elseif ($status->transaction_status == 'pending') {
                toast('Pembayaran masih pending.', 'info');
            } else {
                toast('Pembayaran gagal/expired.', 'error');
            }

            return redirect()->back();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function struk(string $kodestruk)
    {
        $struk = Struk::where('kode_struk', $kodestruk)->first();
        $pemenang = Pemenang::findOrFail($struk->id_pemenang);
        // Hitung total
        $bidakhir = $pemenang->bid + $struk->lelang->barang->harga;
        $adminfee = $bidakhir * 0.05;
        $total = $adminfee + $bidakhir;

        // Bikin Snap Token
        if (!$struk->snap_token) {
            $params = [
                'transaction_details' => [
                    'order_id' => $struk->kode_struk,
                    'gross_amount' => $total,
                ],
                'customer_details' => [
                    'first_name' => $pemenang->user->nama_lengkap,
                    'email' => $pemenang->user->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            $struk->snap_token = $snapToken;
            $struk->save();
        } else {
            $snapToken = $struk->snap_token;
        }

        return view('struk', compact('struk', 'pemenang', 'snapToken'));
    }

}
