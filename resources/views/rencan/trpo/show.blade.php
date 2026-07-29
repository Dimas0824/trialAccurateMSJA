@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])

    <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Detail {{ $title_menu }}</h5>
                <span class="badge bg-gradient-secondary">{{ ucfirst($order->status) }}</span>
            </div>
            <hr class="horizontal dark mt-0">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><small class="text-muted">Nomor</small>
                        <p class="mb-0 font-weight-bold">{{ $order->nomor }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Tanggal</small>
                        <p class="mb-0">{{ $order->tanggal }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Tanggal Pengiriman</small>
                        <p class="mb-0">{{ $order->tanggal_pengiriman ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Pemasok</small>
                        <p class="mb-0">{{ $order->nama_pemasok_snapshot }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Mata Uang</small>
                        <p class="mb-0">{{ $order->mata_uang ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Syarat Pembayaran</small>
                        <p class="mb-0">{{ $order->syarat_pembayaran ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Bank Pemasok</small>
                        <p class="mb-0">{{ $order->bank_pemasok ?: '-' }}</p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Opsi Pajak</small>
                        <p class="mb-0">
                            {{ $order->kena_pajak ? 'Kena Pajak' : 'Tidak Kena Pajak' }}{{ $order->total_termasuk_pajak ? ' - Harga Termasuk Pajak' : '' }}
                        </p>
                    </div>
                    <div class="col-md-4"><small class="text-muted">Keterangan</small>
                        <p class="mb-0">{{ $order->keterangan ?: '-' }}</p>
                    </div>
                </div>

                <hr class="horizontal dark">
                <h6>Rincian Barang/Jasa</h6>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="thead-light" style="background-color: #00b7bd4f;">
                            <tr>
                                <th>Barang/Jasa</th>
                                <th>Kuantitas</th>
                                <th>Harga Satuan</th>
                                <th>Diskon</th>
                                <th>Pajak</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->kode_barang_snapshot }} - {{ $item->nama_barang_snapshot }}</td>
                                    <td>{{ $item->kuantitas }} {{ $item->nama_satuan_snapshot }}</td>
                                    <td>{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                                    <td>{{ $item->diskon_persen }}%</td>
                                    <td>{{ $item->kode_pajak ?: '-' }}</td>
                                    <td class="text-end">{{ number_format($item->total_baris, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="text-end">Subtotal</th>
                                <th class="text-end">{{ number_format($order->subtotal, 2, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">Pajak</th>
                                <th class="text-end">{{ number_format($order->jumlah_pajak, 2, ',', '.') }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">Total</th>
                                <th class="text-end">{{ number_format($order->total_akhir, 2, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-secondary mb-0" type="button"
                    onclick="window.location='{{ url($url_menu) }}'">Kembali</button>
                @if ($authorize->edit == '1' && $order->status === 'draf')
                    <button class="btn btn-primary mb-0" type="button"
                        onclick="window.location='{{ url($url_menu . '/edit/' . encrypt($order->id)) }}'">Edit</button>
                @endif
            </div>
        </div>
    </div>
@endsection
