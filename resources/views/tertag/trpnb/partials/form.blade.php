<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $receipt ? 'Edit' : 'Tambah' }} {{ $title_menu }}</h5>
        </div>
        <form action="{{ $action }}" method="POST">
            <div class="card-body">@csrf @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="row g-3">
                    <div class="col-md-4"><label>No Form</label><input class="form-control" name="nomor_form" required
                            value="{{ old('nomor_form', $receipt?->nomor_form) }}"></div>
                    <div class="col-md-4"><label>Tanggal</label><input class="form-control" type="date"
                            name="tanggal_penerimaan" required
                            value="{{ old('tanggal_penerimaan', $receipt?->tanggal_penerimaan ?? now()->toDateString()) }}">
                    </div>
                    <div class="col-md-4"><label>Nomor Penerimaan Pemasok</label><input class="form-control"
                            name="nomor_penerimaan_pemasok"
                            value="{{ old('nomor_penerimaan_pemasok', $receipt?->nomor_penerimaan_pemasok) }}"></div>
                    <div class="col-md-12"><label>Pemasok</label><select class="form-select" name="pemasok_id" required>
                            <option value=""></option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(old('pemasok_id', $receipt?->pemasok_id) == $supplier->id)>{{ $supplier->nama }}
                                </option>
                            @endforeach
                        </select></div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h6>Barang</h6><button class="btn btn-secondary btn-sm" type="button" id="add-item">Tambah
                        Baris</button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Referensi Detail PO</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="items"></tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer"><button class="btn btn-primary mb-0">Simpan</button><a
                    class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">Kembali</a></div>
        </form>
    </div>
</div>
@push('js')
    <script>
        const goods = @json($goods),
            lines = @json($orderLines),
            items = @json(old('items', $items));
        let n = 0;

        function row(i = {}) {
            let k = n++;
            $('#items').append(
                `<tr><td><select class="form-select" name="items[${k}][barang_jasa_kode]" required><option></option>${goods.map(x=>`<option value="${x.kode_barang}" ${x.kode_barang===i.barang_jasa_kode?'selected':''}>${x.kode_barang} - ${x.nama_barang}</option>`).join('')}</select></td><td><input class="form-control" type="number" min="0.0001" step="0.0001" name="items[${k}][kuantitas]" value="${i.kuantitas??1}" required></td><td><select class="form-select" name="items[${k}][rincian_pesanan_pembelian_id]"><option></option>${lines.map(x=>`<option value="${x.id}" ${String(x.id)===String(i.rincian_pesanan_pembelian_id)?'selected':''}>#${x.id} - ${x.barang_jasa_kode}</option>`).join('')}</select></td><td><button type="button" class="btn btn-danger btn-sm remove">×</button></td></tr>`
                )
        }
        $(function() {
            (items.length ? items : [{}]).forEach(row);
            $('#add-item').click(() => row());
            $('#items').on('click', '.remove', function() {
                $(this).closest('tr').remove()
            })
        })
    </script>
@endpush
