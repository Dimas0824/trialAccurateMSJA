@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('content')
    @include('layouts.navbars.auth.topnav', ['title' => ''])

    <style>
        .purchase-report .report-filter {
            background: #f7fbfc;
            border: 1px solid #d9edf0;
            border-radius: .75rem;
        }

        .purchase-report .filter-label {
            color: #5c6f82;
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .purchase-report .metric {
            border: 1px solid #e4edf2;
            border-radius: .7rem;
            min-height: 94px;
        }

        .purchase-report .metric-doc {
            border-left: 4px solid #00a6a6;
        }

        .purchase-report .metric-total {
            border-left: 4px solid #2dce89;
        }
    </style>

    <div class="container-fluid purchase-report">
        <div class="row">
            <div class="col-md-12">
                <div class="row mx-1">
                    <div class="card mb-4">
                        <div class="row">
                            <div class="card-header col-md-auto">
                                <p class="text-uppercase text-xs font-weight-bolder text-primary mb-1">Ringkasan dokumen</p>
                                <h5 class="mb-0">{{ $title_menu }}</h5>
                                <p class="text-sm text-secondary mb-0">Saring, tinjau, lalu ekspor dokumen pembelian.</p>
                            </div>
                        </div>

                        <hr class="horizontal dark mt-0">

                        <div class="row px-4 py-2">
                            <div class="col-12">
                                <div class="report-filter p-3">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                                            <label class="filter-label">Rentang tanggal</label>
                                            <div class="input-group">
                                                <input class="form-control" name="date_from" type="date" value="{{ now()->startOfMonth()->toDateString() }}">
                                                <span class="input-group-text px-2">&mdash;</span>
                                                <input class="form-control" name="date_to" type="date" value="{{ now()->endOfMonth()->toDateString() }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-4 mb-3 mb-lg-0">
                                            <label class="filter-label">Pemasok</label>
                                            <select class="form-select" name="supplier_id">
                                                <option value="">Semua pemasok</option>
                                                @foreach ($suppliers as $supplier)
                                                    <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-2 col-md-4 mb-3 mb-lg-0">
                                            <label class="filter-label">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="">Semua status</option>
                                                @foreach ($statuses as $status)
                                                    <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if ($report !== 'rppnb')
                                            <div class="col-lg-2 col-md-4 mb-3 mb-lg-0">
                                                <label class="filter-label">Mata uang</label>
                                                <select class="form-select" name="currency_id">
                                                    <option value="">Semua mata uang</option>
                                                    @foreach ($currencies as $currency)
                                                        <option value="{{ $currency->id }}">{{ $currency->kode }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                        <div class="{{ $report === 'rppnb' ? 'col-lg-3' : 'col-lg-3' }} col-md-6">
                                            <label class="filter-label">Pencarian cepat</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                <input class="form-control" name="keyword" placeholder="Nomor atau pemasok">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row px-4 py-2">
                            <div class="col-lg d-flex justify-content-end">
                                <button class="btn btn-primary mb-0" id="filter">
                                    <i class="fas fa-filter me-1"></i>Terapkan filter
                                </button>
                            </div>
                        </div>

                        <div class="row px-4 py-2">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="metric metric-doc p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-1 text-secondary">Dokumen ditemukan</p>
                                        <h3 class="mb-0" id="summary-documents">0</h3>
                                    </div>
                                    <i class="ni ni-single-copy-04 text-primary text-lg"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric metric-total p-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <p class="text-sm mb-1 text-secondary">Total mata uang terpilih</p>
                                        <h3 class="mb-0" id="summary-total">-</h3>
                                    </div>
                                    <i class="ni ni-money-coins text-success text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row px-4 py-2">
                            <div class="col-lg d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <p class="text-sm text-secondary mb-0">Daftar dokumen sesuai filter</p>
                                <div>
                                    <button class="btn btn-success mb-0 export" data-format="excel"><i class="fas fa-file-excel me-1"></i>Excel</button>
                                    <button class="btn btn-danger mb-0 export" data-format="pdf"><i class="fas fa-file-pdf me-1"></i>PDF</button>
                                    <button class="btn btn-secondary mb-0 export" data-format="print"><i class="fas fa-print me-1"></i>Print</button>
                                </div>
                            </div>
                        </div>

                        {{-- Struktur ini sama dengan list rencan agar gutter tabel mengikuti lebar card. --}}
                        <div class="row px-4 py-2">
                            <div class="table-responsive">
                                <table class="table display" id="report_table_{{ $dmenu }}">
                                    <thead class="thead-light" style="background-color: #00b7bd4f;">
                                        <tr>
                                            <th>No</th>
                                            <th>Nomor</th>
                                            <th>Tanggal</th>
                                            <th>Pemasok</th>
                                            <th>Mata Uang</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row px-4 py-2">
                            <div class="col-lg">
                                <div class="nav-wrapper" id="noted">
                                    <code>Note : <i aria-hidden="true" style="color: #ffc2cd;" class="fas fa-circle"></i> Dokumen dibatalkan</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            const endpoint = '{{ url("report-pembelian/$report") }}';
            const filters = () => ({
                date_from: $('[name=date_from]').val(),
                date_to: $('[name=date_to]').val(),
                supplier_id: $('[name=supplier_id]').val(),
                status: $('[name=status]').val(),
                currency_id: $('[name=currency_id]').val(),
                keyword: $('[name=keyword]').val(),
            });

            const table = $('#report_table_{{ $dmenu }}').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                lengthMenu: [10, 25, 50, 100],
                ajax: {
                    url: endpoint + '/data',
                    data: data => {
                        Object.assign(data, filters());
                        data.sort = ['nomor', 'nomor', 'tanggal', 'pemasok', 'mata_uang', 'nilai', 'status'][data.order[0].column];
                        data.direction = data.order[0].dir;
                    },
                },
                columns: [
                    { data: null, orderable: false, render: (data, type, row, meta) => meta.row + meta.settings._iDisplayStart + 1 },
                    { data: 'nomor' },
                    { data: 'tanggal' },
                    { data: 'pemasok' },
                    { data: 'mata_uang' },
                    { data: 'nilai', render: $.fn.dataTable.render.number('.', ',', 2, '') },
                    { data: 'status' },
                ],
            });

            $('#report_table_{{ $dmenu }}').on('xhr.dt', (event, settings, json) => {
                $('#summary-documents').text(json.summary.document_count);
                $('#summary-total').text(json.summary.total ?? '-');
            });

            $('#filter').click(() => table.ajax.reload());
            $('.export').click(function() {
                window.open(endpoint + '/export/' + $(this).data('format') + '?' + $.param(filters()), $(this).data('format') === 'print' ? '_blank' : '_self');
            });
        });
    </script>
@endpush