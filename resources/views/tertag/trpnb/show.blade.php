@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid"><div class="card"><div class="card-header"><h5 class="mb-0">{{ $receipt->nomor_form }} — {{ $receipt->pemasok }}</h5></div><div class="card-body"><p>Tanggal: {{ $receipt->tanggal_penerimaan }}</p><div class="table-responsive"><table class="table"><thead><tr><th>Barang</th><th>Qty</th><th>Satuan</th></tr></thead><tbody>@foreach($items as $item)<tr><td>{{ $item->nama_barang_snapshot }}</td><td>{{ $item->kuantitas }}</td><td>{{ $item->satuan_snapshot }}</td></tr>@endforeach</tbody></table></div></div><div class="card-footer"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a>@if($authorize->edit == '1' && $receipt->status === 'draf')<a class="btn btn-primary mb-0" href="{{ url($url_menu . '/edit/' . encrypt($receipt->id)) }}">Edit</a>@endif</div></div></div>
@endsection
