<?php

namespace App\Providers;

use App\Models\Bid;
use App\Models\Kategori;
use Str;
use App\Models\Lelang;  
use App\Models\Pemenang;
use App\Models\Struk;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;


class LelangCekPemenang extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $kategoris = Kategori::all();
        View::share('kategoris', $kategoris);
        // Jalan di setiap request, auto check lelang yang udah berakhir
        View::composer('*', function () {
            $lelangs = Lelang::latest()->get();
            foreach($lelangs as $lelang){
                $now = now();
                if($now->lt($lelang->jadwal_mulai)) {
                    $status = 'ditutup';
                } elseif($now->between($lelang->jadwal_mulai, $lelang->jadwal_berakhir)){
                    $status = 'dibuka';
                } else {
                    $status = 'selesai';
                }

                if($lelang->status !== $status){
                    $lelang->status = $status;
                    $lelang->save();
                }
            }

            $lelangBerakhir = Lelang::where('jadwal_berakhir', '<=', Carbon::now())
                ->whereDoesntHave('pemenang') // pastikan belum ada pemenang
                ->with('bid')
                ->get();

            foreach ($lelangBerakhir as $lelang) {
                $pemenang = $lelang->bid()
                    ->orderByDesc('bid') // pastikan 'bid' adalah kolom nilai
                    ->orderByDesc('created_at')
                    ->first();
                if ($pemenang) {
                    $newPemenang = Pemenang::create([
                        'id_lelang' => $lelang->id,
                        'id_user'   => $pemenang->id_user,
                        'bid'       => $pemenang->bid, 
                    ]);
                    // generate kode lelang: L + 6 huruf random kapital
                    $kodeStruk = 'STRL-' . Str::upper(Str::random(10));

                    // cek biar gak duplikat (kecil kemungkinan sih, tapi tetep aman)
                    while (Struk::where('kode_struk', $kodeStruk)->exists()) {
                        $kodeStruk = 'STRL-' . Str::upper(Str::random(10));
                    }
                    $total = $pemenang->bid + $lelang->barang->harga;
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
            }
            $now = now();

            $habiswaktubayar = Struk::where('status', 'belum dibayar')->get();
            foreach($habiswaktubayar as $struk)
            {
                $bataswaktu = $struk->tgl_trx->addHour();
                if($now->gt($bataswaktu)){
                    $struk->status = 'pending';
                    $struk->save();
                }
            }

            $habiswaktupending = Struk::where('status', 'pending')->get();
            foreach($habiswaktupending as $struk){
                $bataswaktu = $struk->updated_at->addHour();
                if($now->gt($bataswaktu)){
                    $struk->status = 'gagal';
                    $struk->save();
                }
            }
            $struk = Struk::where('status', 'gagal')->get();
            foreach($struk as $lelanggagal){
                $lelangGaDibayar = Lelang::where('id', $lelanggagal->id_lelang)->first();
                if($lelangGaDibayar){
                    $struk = Struk::where('id_lelang', $lelangGaDibayar->id)->delete();
                    $pemenang = Pemenang::where('id_lelang', $lelangGaDibayar->id)->delete();
    
                    $bid = Bid::where('id_lelang', $lelangGaDibayar->id)->delete();
    
                    $jadwalBaru = $now->copy()->addHour();
                    $jadwalberakhir = $jadwalBaru->copy()->addHours(3);
                    $lelangGaDibayar->jadwal_mulai = $jadwalBaru;
                    $lelangGaDibayar->jadwal_berakhir = $jadwalberakhir;
                    $lelangGaDibayar->save();
                }

            }
        });
    }
}