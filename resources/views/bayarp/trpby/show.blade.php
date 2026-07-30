@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $payment->nomor_bukti }}</h5>
            </div>
            <div class="card-body">
                <p>Tanggal: {{ $payment->tanggal_pembayaran }}</p>
                <p>Nilai: {{ number_format($payment->nilai_pembayaran, 2, ',', '.') }}</p>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Terutang</th>
                            <th>Bayar</th>
                            <th>Diskon</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->nomor_form }}</td>
                                <td>{{ number_format($item->jumlah_terutang, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->jumlah_bayar, 2, ',', '.') }}</td>
                                <td>{{ number_format($item->jumlah_diskon, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a></div>
        </div>
    </div>
@endsection
