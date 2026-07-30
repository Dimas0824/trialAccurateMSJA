@extends('layouts.app',['class'=>'g-sidenav-show bg-gray-100'])
@section('content') @include('layouts.navbars.auth.topnav',['title'=>'']) @include('koreks.trretp.partials.form',['action'=>url($url_menu.'/edit/'.encrypt($return->id)),'method'=>'PUT']) @endsection
