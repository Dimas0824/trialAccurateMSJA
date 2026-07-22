@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])
{{-- Mengikuti struktur tampilan tabel master otomatis MSJFramework. --}}
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
                            <div class="col">
                                @include('components.alert')
                            </div>
                        </div>
                        <hr class="horizontal dark mt-0">
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper">
                                    @if ($authorize->add == '1')
                                        <button class="btn btn-primary mb-0" onclick="window.location='{{ URL::to($url_menu . '/add') }}'">
                                            <i class="fas fa-plus me-1"></i><span class="font-weight-bold">Tambah</span>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="list_{{ $dmenu }}">
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            <th width="110">Action</th>
                                            <th>No</th>
                                            <th>ID Pemasok</th>
                                            <th>Nama Pemasok</th>
                                            <th>Kategori</th>
                                            <th>Tipe Pemasok</th>
                                            <th>Telepon</th>
                                            <th>Email</th>
                                            <th>Saldo Utang</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($suppliers as $supplier)
                                            <tr {{ $supplier->isactive == '0' ? 'class=not style=background-color:#ffe9ed;' : '' }}>
                                                <td class="text-sm font-weight-normal">
                                                    <div class="btn-group">
                                                        <button class="btn btn-primary btn-sm mb-0 px-3" type="button" title="View Data"
                                                            onclick="window.location='{{ url($url_menu . '/edit/' . encrypt($supplier->id)) }}'">
                                                            <i class="fas fa-eye"></i><span class="font-weight-bold"> View</span>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary mb-0 px-3 dropdown-toggle dropdown-toggle-split"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <span class="visually-hidden">Toggle Dropdown</span>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if ($authorize->edit == '1' && $supplier->isactive == '1')
                                                                <li><hr class="dropdown-divider"></li>
                                                                <button type="button" class="btn btn-sm btn-warning mx-2 mb-0 w-90" title="Edit Data"
                                                                    onclick="window.location='{{ url($url_menu . '/edit/' . encrypt($supplier->id)) }}'">
                                                                    <i class="fas fa-edit"></i><span class="font-weight-bold"> Edit</span>
                                                                </button>
                                                            @endif
                                                            @if ($authorize->delete == '1')
                                                                <form action="{{ url($url_menu . '/' . encrypt($supplier->id)) }}" method="POST" style="display: inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <li><hr class="dropdown-divider"></li>
                                                                    <button type="submit" class="btn btn-sm btn-{{ $supplier->isactive == '0' ? 'success' : 'danger' }} mx-2 mb-0 w-90"
                                                                        title="Ubah Status"
                                                                        onclick="return confirm('{{ $supplier->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }} pemasok ini?')">
                                                                        <i class="fas fa-random"></i><span class="font-weight-bold"> {{ $supplier->isactive == '0' ? 'Aktifkan' : 'Non Aktifkan' }}</span>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                        </ul>
                                                    </div>
                                                </td>
                                                <td>{{ $loop->iteration }}</td>
                                                <td class="text-sm">{{ $supplier->kode_pemasok }}</td>
                                                <td class="text-sm font-weight-bold text-dark">{{ $supplier->nama }}</td>
                                                <td class="text-sm">{{ $supplier->kategori ?? '' }}</td>
                                                <td class="text-sm">{{ $supplier->tipe_pemasok ? ucfirst($supplier->tipe_pemasok) : '' }}</td>
                                                <td class="text-sm">{{ $supplier->telepon_bisnis ?? $supplier->nomor_handphone ?? '' }}</td>
                                                <td class="text-sm">{{ $supplier->email ?? '' }}</td>
                                                <td class="text-sm">{{ number_format($supplier->saldo_utang, 2, ',', '.') }}</td>
                                                <td class="text-sm">{{ $supplier->isactive == '1' ? 'Aktif' : 'Tidak Aktif' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper" id="noted"><code>Note : <i aria-hidden="true" style="color: #ffc2cd;" class="fas fa-circle"></i> Data not active</code></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
