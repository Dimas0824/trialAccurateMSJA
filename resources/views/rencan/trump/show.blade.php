@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])

    <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail {{ $title_menu }}</h5>
                <span class="badge bg-gradient-secondary">{{ ucfirst($advance->status) }}</span>
            </div>
            <hr class="horizontal dark mt-0">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><small class="text-muted">Nomor Form</small>
                        <p class="mb-0 font-weight-bold">{{ $advance->nomor_form }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Tanggal</small>
                        <p class="mb-0">{{ $advance->tanggal }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Nomor Faktur Pemasok</small>
                        <p class="mb-0">{{ $advance->nomor_faktur_pemasok }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Pemasok</small>
                        <p class="mb-0">{{ $advance->nama_pemasok_snapshot }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Pesanan Pembelian</small>
                        <p class="mb-0">{{ $advance->nomor_pesanan ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Mata Uang</small>
                        <p class="mb-0">{{ $advance->mata_uang ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Syarat Pembayaran</small>
                        <p class="mb-0">{{ $advance->syarat_pembayaran ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Bank Pemasok</small>
                        <p class="mb-0">{{ $advance->bank_pemasok ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Opsi Pajak</small>
                        <p class="mb-0">
                            {{ $advance->kena_pajak ? 'Kena Pajak' : 'Tidak Kena Pajak' }}{{ $advance->total_termasuk_pajak ? ' - Termasuk Pajak' : '' }}
                        </p>
                    </div>
                    <div class="col-md-6"><small class="text-muted">Alamat</small>
                        <p class="mb-0">{{ $advance->alamat ?: '-' }}</p>
                    </div>
                    <div class="col-md-6"><small class="text-muted">Keterangan</small>
                        <p class="mb-0">{{ $advance->keterangan ?: '-' }}</p>
                    </div>
                </div>
                <hr class="horizontal dark">
                <div class="row">
                    <div class="col-md-4 ms-auto"><small class="text-muted">Subtotal</small>
                        <p class="mb-0">{{ number_format($advance->subtotal, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Pajak</small>
                        <p class="mb-0">{{ number_format($advance->jumlah_pajak, 2, ',', '.') }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Total Uang Muka</small>
                        <p class="mb-0 font-weight-bold">{{ number_format($advance->total_akhir, 2, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-secondary mb-0" type="button"
                    onclick="window.location='{{ url($url_menu) }}'">Kembali</button>
                @if ($authorize->edit == '1' && $advance->status === 'draf')
                    <button class="btn btn-primary mb-0" type="button"
                        onclick="window.location='{{ url($url_menu . '/edit/' . encrypt($advance->id)) }}'">Edit</button>
                @endif
            </div>
        </div>
    </div>
@endsection
