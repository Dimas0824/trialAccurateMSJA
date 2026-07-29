@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    @include('rencan.trpo.form', [
        'action' => url($url_menu . '/edit/' . encrypt($order->id)),
        'method' => 'PUT',
    ])
@endsection
