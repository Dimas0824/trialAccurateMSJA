{{-- Grid tiga kolom menjaga setiap baris form sejajar dan mudah dipindai. --}}
<style>
    .trpo-form-shell .card { border: 1px solid #dbe4ee; box-shadow: 0 .35rem 1rem rgba(15, 23, 42, .08); }
    .trpo-form-shell .card-header { border-bottom: 1px solid #dbe4ee; padding: 1.15rem 1.35rem; }
    .trpo-form-shell .trpo-action-bar { background: #f8fafc; }
    .trpo-form-shell .form-control-label { color: #172b4d; font-weight: 700; margin-bottom: .45rem; }
    .trpo-form-shell .form-control, .trpo-form-shell .form-select { border-color: #cbd5e1; min-height: 2.75rem; }
    .trpo-form-shell .form-control:focus, .trpo-form-shell .form-select:focus { border-color: #00a6ad; box-shadow: 0 0 0 .18rem rgba(0, 166, 173, .14); }
    .trpo-form-shell .form-check { color: #344767; }
    .trpo-form-shell .trpo-tax-options { background: #f8fafc; border-color: #cbd5e1 !important; }
    .trpo-form-shell .trpo-detail-heading { border-bottom: 2px solid #00a6ad; padding-bottom: .65rem; }
    .trpo-form-shell .trpo-detail-table { border-color: #b8dfe2 !important; }
    .trpo-form-shell .trpo-detail-table thead { background: #00b7bd4f; color: #172b4d; }
    .trpo-form-shell .trpo-detail-table th { border-bottom: 2px solid #77cfd4; font-size: .75rem; font-weight: 700; letter-spacing: .02em; text-transform: uppercase; }
    .trpo-form-shell .trpo-detail-table td { border-color: #e2e8f0; vertical-align: middle; }
</style>
<div class="container-fluid py-3 trpo-form-shell">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $order ? 'Edit' : 'Tambah' }} {{ $title_menu }}</h5>
        </div>
        <hr class="horizontal dark mt-0">
        <div class="card-body p-3 border-bottom trpo-action-bar"><div class="nav-wrapper"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}"><i class="fas fa-circle-left me-1"></i><span class="font-weight-bold">Kembali</span></a><button class="btn btn-primary mb-0" form="trpo-form" type="submit"><i class="fas fa-floppy-disk me-1"></i><span class="font-weight-bold">Simpan</span></button></div></div>
        <form action="{{ $action }}" id="trpo-form" class="trpo-order-form" method="POST">
            <div class="card-body">@csrf @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="row g-3 trpo-form-grid">
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Nomor</label><input
                                class="form-control" name="nomor" maxlength="50" required
                                value="{{ old('nomor', $order?->nomor) }}">
                            @error('nomor')
                                <p class="text-danger text-xs pt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Tanggal</label><input
                                class="form-control" type="date" name="tanggal" required
                                value="{{ old('tanggal', $order?->tanggal ?? now()->toDateString()) }}"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Tanggal Pengiriman</label><input
                                class="form-control" type="date" name="tanggal_pengiriman"
                                value="{{ old('tanggal_pengiriman', $order?->tanggal_pengiriman) }}"></div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Pemasok</label><select
                                class="form-select" name="pemasok_id" required>
                                <option value=""></option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('pemasok_id', $order?->pemasok_id) == $supplier->id)>
                                        {{ $supplier->kode_pemasok }} - {{ $supplier->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Mata Uang</label><select
                                class="form-select" name="mata_uang_id">
                                <option value=""></option>
                                @foreach ($currencies as $currency)
                                    <option value="{{ $currency->id }}" @selected(old('mata_uang_id', $order?->mata_uang_id) == $currency->id)>
                                        {{ $currency->kode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Syarat Pembayaran</label><select
                                class="form-select" name="syarat_pembayaran_id">
                                <option value=""></option>
                                @foreach ($terms as $term)
                                    <option value="{{ $term->id }}" @selected(old('syarat_pembayaran_id', $order?->syarat_pembayaran_id) == $term->id)>
                                        {{ $term->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Bank Pemasok</label><select
                                class="form-select" name="rekening_bank_pemasok_id">
                                <option value=""></option>
                                @foreach ($banks as $bank)
                                    <option value="{{ $bank->id }}" @selected(old('rekening_bank_pemasok_id', $order?->rekening_bank_pemasok_id) == $bank->id)>
                                        {{ $bank->nama_bank }} - {{ $bank->nomor_rekening }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Alamat Pengiriman</label><select
                                class="form-select" name="alamat_pengiriman_id">
                                <option value=""></option>
                                @foreach ($addresses as $address)
                                    <option value="{{ $address->id }}" @selected(old('alamat_pengiriman_id', $order?->alamat_pengiriman_id) == $address->id)>
                                        {{ $address->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">Pengiriman</label><select
                                class="form-select" name="metode_pengiriman_id">
                                <option value=""></option>
                                @foreach ($shippingMethods as $shipping)
                                    <option value="{{ $shipping->id }}" @selected(old('metode_pengiriman_id', $order?->metode_pengiriman_id) == $shipping->id)>
                                        {{ $shipping->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group"><label class="form-control-label">FOB</label><select class="form-select"
                                name="ketentuan_fob_id">
                                <option value=""></option>
                                @foreach ($fobs as $fob)
                                    <option value="{{ $fob->id }}" @selected(old('ketentuan_fob_id', $order?->ketentuan_fob_id) == $fob->id)>
                                        {{ $fob->nama }}</option>
                                @endforeach
                            </select></div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group mb-0">
                            <label class="form-control-label">Opsi Pajak</label>
                            <div class="border rounded px-3 py-2 d-flex flex-wrap gap-4 trpo-tax-options">
                                <div class="form-check mb-0"><input class="form-check-input" type="checkbox"
                                        name="kena_pajak" value="1" @checked(old('kena_pajak', $order?->kena_pajak))><label
                                        class="form-check-label">Kena Pajak</label></div>
                                <div class="form-check mb-0"><input class="form-check-input" type="checkbox"
                                        name="total_termasuk_pajak" value="1" @checked(old('total_termasuk_pajak', $order?->total_termasuk_pajak))><label
                                        class="form-check-label">Harga Termasuk Pajak</label></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group"><label class="form-control-label">Keterangan</label>
                            <textarea class="form-control" name="keterangan">{{ old('keterangan', $order?->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>
                <hr class="horizontal dark">
                <div class="d-flex justify-content-between align-items-center trpo-detail-heading">
                    <h6 class="mb-0">Rincian Barang/Jasa</h6><button type="button"
                        class="btn btn-secondary btn-sm mb-0" id="add-item"><i class="fas fa-plus me-1"></i>Tambah
                        Baris</button>
                </div>
                <div class="table-responsive mt-3 border rounded overflow-hidden trpo-detail-table">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Barang/Jasa</th>
                                <th width="110">Kuantitas</th>
                                <th width="150">Harga Satuan</th>
                                <th width="110">Diskon %</th>
                                <th>Pajak</th>
                                <th>Keterangan</th>
                                <th width="40"></th>
                            </tr>
                        </thead>
                        <tbody id="items"></tbody>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
@push('js')
    <script>
        // Data master dipakai untuk menambah baris rincian tanpa memuat ulang halaman.
        const goods = @json($goods);
        const taxes = @json($taxes->values());
        const currentItems = @json(old('items', $items));
        let itemIndex = 0;

        function options(rows, selected, label) {
            return '<option value=""></option>' + rows.map(row =>
                `<option value="${row.id}" ${String(row.id) === String(selected ?? '') ? 'selected' : ''}>${label(row)}</option>`
            ).join('');
        }

        function addItem(item = {}) {
            const index = itemIndex++;
            $('#items').append(
                `<tr><td><select class="form-select" name="items[${index}][barang_jasa_kode]" required>${options(goods.map(row => ({...row, id: row.kode_barang})), item.barang_jasa_kode, row => `
                $ {
                    row.kode_barang
                } - $ {
                    row.nama_barang
                }
                `)}</select></td><td><input class="form-control" type="number" min="0.0001" step="0.0001" name="items[${index}][kuantitas]" required value="${item.kuantitas ?? 1}"></td><td><input class="form-control" type="number" min="0" step="0.01" name="items[${index}][harga_satuan]" required value="${item.harga_satuan ?? 0}"></td><td><input class="form-control" type="number" min="0" max="100" step="0.0001" name="items[${index}][diskon_persen]" value="${item.diskon_persen ?? 0}"></td><td><select class="form-select" name="items[${index}][pajak_id]">${options(taxes, item.pajak_id, row => `${row.kode} - ${row.nama}`)}</select></td><td><input class="form-control" name="items[${index}][keterangan]" value="${item.keterangan ?? ''}"></td><td><button type="button" class="btn btn-danger btn-sm mb-0 remove-item"><i class="fas fa-trash"></i></button></td></tr>`
            );
        }
        // Sisakan satu baris agar pesanan selalu memiliki rincian untuk diisi.
        $(function() {
            (currentItems.length ? currentItems : [{}]).forEach(addItem);
            $('#add-item').on('click', () => addItem());
            $('#items').on('click', '.remove-item', function() {
                if ($('#items tr').length > 1) $(this).closest('tr').remove();
            });
        });
    </script>
@endpush
