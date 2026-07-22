@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    @include('master.mspems.form', ['action' => url('mspems'), 'method' => 'POST'])
@endsection
