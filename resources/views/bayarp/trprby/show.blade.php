@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => '']) <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $order->nomor_bukti }}</h5>
            </div>
            <div class="card-body">
                <p>Batas transfer: {{ $order->tanggal_batas_transfer }}</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Pemasok</th>
                            <th>Nilai Bayar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->nomor_form }}</td>
                                <td>{{ $item->pemasok }}</td>
                                <td>{{ number_format($item->jumlah_dialokasikan, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a>
                @if ($authorize->edit == '1' && $order->status === 'draf')
                    <a class="btn btn-primary mb-0" href="{{ url($url_menu . '/edit/' . encrypt($order->id)) }}">Edit</a>
                @endif
            </div>
        </div>
    </div>
@endsection
