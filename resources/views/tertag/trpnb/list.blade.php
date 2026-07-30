@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="row mx-1">
                    <div class="card">
                        <div class="row">
                            <div class="card-header col-md-auto">
                                <h5 class="mb-0">List {{ $title_menu }}</h5>
                            </div>
                            <div class="col">@include('components.alert')</div>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper">
                                    @if ($authorize->add == '1')
                                        <button class="btn btn-primary mb-0"
                                            onclick="window.location='{{ url($url_menu . '/add') }}'"><i
                                                class="fas fa-plus me-1"></i><span
                                                class="font-weight-bold">Tambah</span></button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_{{ $dmenu }}">
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            <th>Action</th>
                                            <th>No</th>
                                            <th>No Form</th>
                                            <th>Tanggal</th>
                                            <th>Pemasok</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($receipts as $receipt)
                                            <tr>
                                                <td><button class="btn btn-primary btn-sm mb-0"
                                                        onclick="window.location='{{ url($url_menu . '/show/' . encrypt($receipt->id)) }}'">View</button>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $receipt->nomor_form }}</td>
                                                <td>{{ $receipt->tanggal_penerimaan }}</td>
                                                <td>{{ $receipt->pemasok }}</td>
                                                <td>{{ ucfirst($receipt->status) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
