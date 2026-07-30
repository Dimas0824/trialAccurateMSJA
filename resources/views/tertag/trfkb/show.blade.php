@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $invoice->nomor_form }} — {{ $invoice->nomor_faktur_pemasok }}</h5>
            </div>
            <div class="card-body">
                <p>Pemasok: {{ $supplierName ?: '-' }}</p>
                <p>Tanggal: {{ $invoice->tanggal_faktur }}</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Satuan</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->nama_barang_snapshot }}</td>
                                    <td>{{ $item->kuantitas }}</td>
                                    <td>{{ $item->satuan_snapshot }}</td>
                                    <td>{{ number_format($item->harga_satuan, 2, ',', '.') }}</td>
                                    <td>{{ number_format($item->total_baris, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a>
                @if ($authorize->edit == '1' && $invoice->status === 'draf')
                    <a class="btn btn-primary mb-0" href="{{ url($url_menu . '/edit/' . encrypt($invoice->id)) }}">Edit</a>
                @endif
            </div>
        </div>
    </div>
@endsection
