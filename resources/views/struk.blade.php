@extends('layouts.kerangkafrontend')
@section('content')
<div class="hero-section style-2">
        <div class="container">
            <ul class="breadcrumb">
                <li>
                    <a href="./index.html">Home</a>
                </li>
                <li>
                    <a href="#0">My Account</a>
                </li>
                <li>
                    <span>Personal profile</span>
                </li>
            </ul>
        </div>
        <div class="bg_img hero-bg bottom_center" data-background="{{ asset('sbidu/assets/images/banner/hero-bg.png') }}"></div>
    </div>
    <section class="dashboard-section padding-bottom mt--240 mt-lg--440 pos-rel">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-12">
                            @if($struk->status == 'belum dibayar')
                            <div class="dash-pro-item mb-30 dashboard-widget">
                                <div class="user">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('gif/belum.gif') }}" alt="user" style="width: 180px; height: auto;">
                                    </div>
                                    <div class="content">
                                        <h5 class="title"><a href="#0">Menunggu Pembayaran</a></h5>
                                        <span class="username">{{$struk->kode_struk}}</span>
                                    </div>
                                </div>
                                <div class="header">
                                    <h4 class="title">Detail Pembayaran</h4>
                                </div>
                                <ul class="dash-pro-body">
                                    <li>
                                        <div class="info-name">Nama Lengkap</div>
                                        <div class="info-value">{{$pemenang->user->nama_lengkap}}</div>
                                    </li>
                                    <li>
                                        <div class="info-name">Nama Lelang</div>
                                        <div class="info-value">{{$struk->lelang->kode_lelang}}-{{$struk->lelang->barang->nama}}</div>
                                    </li>
                                    @php $bidakhir = $pemenang->bid + $struk->lelang->barang->harga; @endphp
                                    <li>
                                        <div class="info-name">Bid Akhir</div>
                                        <div class="info-value">Rp{{ number_format($bidakhir, 0, ',', '.') }}</div>
                                    </li>
                                    @php
                                    $adminfee = $bidakhir * 0.05;
                                    $total = $adminfee + $bidakhir;
                                    @endphp
                                    <li>
                                        <div class="info-name">Biaya Admin</div>
                                        <div class="info-value">Rp{{ number_format($adminfee, 0, ',', '.') }}</div>
                                    </li>
                                    <li>
                                        <div class="info-name" style="font-size: 25px ; font-weight: bold;">Total</div>
                                        <div class="info-value" style="font-size: 25px ; font-weight: bold;">Rp{{ number_format($total, 0, ',', '.') }}</div>
                                    </li>
                                    <hr>
                                    <li>
                                        <div class="info-name" style="font-size: 20px;" align=center>Transfer Ke</div>
                                    </li>
                                    <li>
                                        <ul class="list-unstyled info-value">
                                            <li>
                                                <div class="info-name"><strong>Bank</strong></div>
                                                <div class="info-value"><strong>BCA</strong></div>
                                            </li>
                                            <li>
                                                <div class="info-name"><strong>No. Rekening</strong></div>
                                                <div class="info-value"><strong>1234567890</strong></div>
                                            </li>
                                            <li>
                                                <div class="info-name"><strong>Atas Nama</strong></div>
                                                <div class="info-value"><strong>PT Lelang Makmur</strong></div>
                                            </li>
                                        </ul>
                                    </li>
                                    <hr>
                                    <li>
                                        <p>Lakukan Pembayaran Sebelum {{ $struk->tgl_trx->addHour()->format('H:i d-m-Y') }}</p>
                                    </li>
                                    <li style="text-align: center; margin-top: 20px;">
                                        <button id="pay-button" class="btn btn-success">Bayar Pakai Midtrans</button>
                                    </li>
                                    <li style="text-align: center; margin-top: 20px;">
                                        <form method="POST" action="{{ route('check.status', $struk->kode_struk) }}">
                                            @csrf
                                            <button class="btn btn-warning">Cek Status Pembayaran</button>
                                        </form>

                                    </li>
                                    

                                    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="Mid-client-fHaiIZ8t2z9vsZS4"></script>
                                    <script type="text/javascript">
                                        // For example trigger on button clicked, or any time you need
                                        var payButton = document.getElementById('pay-button');
                                        payButton.addEventListener('click', function () {
                                        // Trigger snap popup. @TODO: Replace TRANSACTION_TOKEN_HERE with your transaction token.
                                        // Also, use the embedId that you defined in the div above, here.
                                        window.snap.pay('{{$struk->snap_token}}');
                                        });
                                    </script>
                                </ul>
                            </div>
                            @elseif($struk->status == 'pending')
                            <div class="dash-pro-item mb-30 dashboard-widget">
                                <div class="user">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('gif/pending.gif') }}" alt="user" style="width: 180px; height: auto;">
                                    </div>
                                    <div class="content">
                                        <h5 class="title"><a href="#0">Menunggu Konfirmasi Pembayaran</a></h5>
                                        <span class="username">{{$struk->kode_struk}}</span>
                                    </div>
                                </div>
                                <div class="header">
                                    <h4 class="title">Detail Pembayaran</h4>
                                </div>
                                <ul class="dash-pro-body">
                                    <li>
                                        <div class="info-name">Nama Lengkap</div>
                                        <div class="info-value">{{$pemenang->user->nama_lengkap}}</div>
                                    </li>
                                    <li>
                                        <div class="info-name">Nama Lelang</div>
                                        <div class="info-value">{{$struk->lelang->kode_lelang}}-{{$struk->lelang->barang->nama}}</div>
                                    </li>
                                    @php $bidakhir = $pemenang->bid + $struk->lelang->barang->harga; @endphp
                                    <li>
                                        <div class="info-name">Bid Akhir</div>
                                        <div class="info-value">Rp{{ number_format($bidakhir, 0, ',', '.') }}</div>
                                    </li>
                                    @php
                                    $adminfee = $bidakhir * 0.05;
                                    $total = $adminfee + $bidakhir;
                                    @endphp
                                    <li>
                                        <div class="info-name">Biaya Admin</div>
                                        <div class="info-value">Rp{{ number_format($adminfee, 0, ',', '.') }}</div>
                                    </li>
                                    <li>
                                        <div class="info-name" style="font-size: 25px ; font-weight: bold;">Total</div>
                                        <div class="info-value" style="font-size: 25px ; font-weight: bold;">Rp{{ number_format($total, 0, ',', '.') }}</div>
                                    </li>
                                    <hr>
                                    <li>
                                        <p>Pembayaran sedang di verifikasi oleh admin.</p>
                                    </li>
                                </ul>
                            </div>
                            @elseif($struk->status == 'berhasil')
                            <div class="dash-pro-item mb-30 dashboard-widget">
                                <div class="user">
                                    <div class="text-center mb-3">
                                        <img src="{{ asset('gif/success.gif') }}" alt="user" style="width: 180px; height: auto;">
                                    </div>
                                    <div class="content">
                                        <h5 class="title"><a href="#0">Pembayaran Telah Diterima</a></h5>
                                        <span class="username">{{$struk->kode_struk}}</span>
                                    </div>
                                </div>
                                <div class="header">
                                    <h4 class="title">Detail Pembayaran</h4>
                                </div>
                                <ul class="dash-pro-body">
                                    <li>
                                        <div class="info-name">Nama Lengkap</div>
                                        <div class="info-value">{{$pemenang->user->nama_lengkap}}</div>
                                    </li>
                                    <li>
                                        <div class="info-name">Nama Lelang</div>
                                        <div class="info-value">{{$struk->lelang->kode_lelang}}-{{$struk->lelang->barang->nama}}</div>
                                    </li>
                                    @php $bidakhir = $pemenang->bid + $struk->lelang->barang->harga; @endphp
                                    <li>
                                        <div class="info-name">Bid Akhir</div>
                                        <div class="info-value">Rp{{ number_format($bidakhir, 0, ',', '.') }}</div>
                                    </li>
                                    @php
                                    $adminfee = $bidakhir * 0.05;
                                    $total = $adminfee + $bidakhir;
                                    @endphp
                                    <li>
                                        <div class="info-name">Biaya Admin</div>
                                        <div class="info-value">Rp{{ number_format($adminfee, 0, ',', '.') }}</div>
                                    </li>
                                    <li>
                                        <div class="info-name" style="font-size: 25px ; font-weight: bold;">Total</div>
                                        <div class="info-value" style="font-size: 25px ; font-weight: bold;">Rp{{ number_format($total, 0, ',', '.') }}</div>
                                    </li>
                                    <hr>
                                    <li>
                                        <div class="info-name" style="font-size: 25px ; font-weight: bold;">Kode Unik</div>
                                        <div class="info-value" style="font-size: 25px ; font-weight: bold;">{{ $struk->kode_unik}}</div>
                                    </li>
                                    <hr>
                                    <li style="margin-bottom: 20px;">
                                        <p>Pengambilan secara offline di SMK ASSALAAM BANDUNG</p>
                                    </li>
                                    <li>
                                        <p>Atau Hubungi +62-895-0998-3660 via WhatsApp untuk Pengiriman lebih lanjut.</p>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection