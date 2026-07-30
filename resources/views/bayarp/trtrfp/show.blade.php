@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => '']) <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Transfer {{ $transfer->tanggal_transfer }}</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaksi</th>
                            <th>Rekening Pemasok</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->nomor_form }}</td>
                                <td>{{ $item->nama_bank }} {{ $item->nomor_rekening }}</td>
                                <td>{{ number_format($item->jumlah_transfer, 2, ',', '.') }}</td>
                                <td>{{ $item->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a>
                @if ($authorize->edit == '1' && $transfer->status === 'draf')
                    <a class="btn btn-primary mb-0" href="{{ url($url_menu . '/edit/' . encrypt($transfer->id)) }}">Edit</a>
                @endif
            </div>
        </div>
    </div>
@endsection
