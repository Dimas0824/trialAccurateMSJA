@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    @include('rencan.trump.partials.form', ['action' => url($url_menu . '/edit/' . encrypt($advance->id)), 'method' => 'PUT'])
@endsection
