@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => '']) @include('tertag.trpnb.partials.form', ['action' => url($url_menu), 'method' => 'POST'])
@endsection
