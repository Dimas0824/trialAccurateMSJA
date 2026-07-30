@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => '']) <div class="container-fluid py-3">
        <div class="alert alert-warning">Pembayaran tidak dapat diedit. Batalkan dokumen lalu buat pembayaran baru.</div>
    </div>
@endsection
