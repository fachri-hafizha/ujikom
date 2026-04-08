<?php

namespace App\Providers;

use App\Models\Bid;
use App\Models\Kategori;
use Illuminate\Support\Str;
use App\Models\Lelang;  
use App\Models\Pemenang;
use App\Models\Struk;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

class LelangCekPemenang extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Hindari error kalau tabel belum ada
        if (Schema::hasTable('kategoris')) {
            View::share('kategoris', Kategori::all());
        }

        View::composer('*', function () {

            // ❌ STOP kalau tabel belum siap (penting banget pas migrate / deploy)
            if (
                !Schema::hasTable('lelangs') ||
                !Schema::hasTable('bids') ||
                !Schema::hasTable('pemenangs') ||
                !Schema::hasTable('struks')
            ) {
                return;
            }

            $now = now();

            // =========================
            // ✅ UPDATE STATUS LELANG
            // =========================
            $lelangs = Lelang::latest()->get();

            foreach ($lelangs as $lelang) {
                if ($now->lt($lelang->jadwal_mulai)) {
                    $status = 'ditutup';
                } elseif ($now->between($lelang->jadwal_mulai, $lelang->jadwal_berakhir)) {
                    $status = 'dibuka';
                } else {
                    $status = 'selesai';
                }

                if ($lelang->status !== $status) {
                    $lelang->update(['status' => $status]);
                }
            }

            // =========================
            // ✅ TENTUKAN PEMENANG
            // =========================
            $lelangBerakhir = Lelang::where('jadwal_berakhir', '<=', $now)
                ->whereDoesntHave('pemenang')
                ->with(['bid', 'barang'])
                ->get();

            foreach ($lelangBerakhir as $lelang) {

                $bidTerbesar = $lelang->bid()
                    ->orderByDesc('bid')
                    ->orderByDesc('created_at')
                    ->first();

                if (!$bidTerbesar) continue;

                $newPemenang = Pemenang::create([
                    'id_lelang' => $lelang->id,
                    'id_user'   => $bidTerbesar->id_user,
                    'bid'       => $bidTerbesar->bid,
                ]);

                // =========================
                // ✅ GENERATE KODE STRUK
                // =========================
                do {
                    $kodeStruk = 'STRL-' . Str::upper(Str::random(10));
                } while (Struk::where('kode_struk', $kodeStruk)->exists());

                $total = $bidTerbesar->bid + ($lelang->barang->harga ?? 0);
                $adminfee = $total * 0.05;
                $grandtotal = $total + $adminfee;

                Struk::create([
                    'id_lelang'   => $lelang->id,
                    'id_barang'   => $lelang->id_barang,
                    'id_pemenang' => $newPemenang->id,
                    'total'       => $grandtotal,
                    'status'      => 'belum dibayar',
                    'kode_unik'   => null,
                    'tgl_trx'     => now(),
                    'kode_struk'  => $kodeStruk,
                ]);
            }

            // =========================
            // ✅ BELUM BAYAR → PENDING
            // =========================
            Struk::where('status', 'belum dibayar')
                ->get()
                ->each(function ($struk) use ($now) {
                    if ($now->gt($struk->tgl_trx->copy()->addHour())) {
                        $struk->update(['status' => 'pending']);
                    }
                });

            // =========================
            // ✅ PENDING → GAGAL
            // =========================
            Struk::where('status', 'pending')
                ->get()
                ->each(function ($struk) use ($now) {
                    if ($now->gt($struk->updated_at->copy()->addHour())) {
                        $struk->update(['status' => 'gagal']);
                    }
                });

            // =========================
            // ✅ HANDLE GAGAL (RESET LELANG)
            // =========================
            Struk::where('status', 'gagal')->get()
                ->each(function ($struk) use ($now) {

                    $lelang = Lelang::find($struk->id_lelang);
                    if (!$lelang) return;

                    // hapus semua terkait
                    Struk::where('id_lelang', $lelang->id)->delete();
                    Pemenang::where('id_lelang', $lelang->id)->delete();
                    Bid::where('id_lelang', $lelang->id)->delete();

                    // reset jadwal
                    $mulai = $now->copy()->addHour();
                    $selesai = $mulai->copy()->addHours(3);

                    $lelang->update([
                        'jadwal_mulai'   => $mulai,
                        'jadwal_berakhir'=> $selesai,
                        'status'         => 'ditutup',
                    ]);
                });

        });
    }
}