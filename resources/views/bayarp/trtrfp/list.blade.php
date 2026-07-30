@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => '']) <div class="container-fluid">
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
                                            onclick="window.location='{{ url($url_menu . '/add') }}'">Tambah</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_{{ $dmenu }}">
                                    <thead class="thead-light" style="background-color:#00b7bd4f">
                                        <tr>
                                            <th>Action</th>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Bank Sumber</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($transfers as $transfer)
                                            <tr>
                                                <td><button class="btn btn-primary btn-sm mb-0"
                                                        onclick="window.location='{{ url($url_menu . '/show/' . encrypt($transfer->id)) }}'">View</button>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $transfer->tanggal_transfer }}</td>
                                                <td>{{ $transfer->bank_sumber }}</td>
                                                <td>{{ number_format($transfer->total_transfer, 2, ',', '.') }}</td>
                                                <td>{{ ucfirst($transfer->status) }}</td>
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
