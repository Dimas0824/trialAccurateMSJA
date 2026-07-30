<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $order ? 'Edit' : 'Tambah' }} {{ $title_menu }}</h5>
        </div>
        <div class="card-body p-3 border-bottom"><div class="nav-wrapper"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}"><i class="fas fa-circle-left me-1"></i><span class="font-weight-bold">Kembali</span></a><button class="btn btn-primary mb-0" form="trprby-form" type="submit"><i class="fas fa-floppy-disk me-1"></i><span class="font-weight-bold">Simpan</span></button></div></div>
        <form action="{{ $action }}" id="trprby-form" method="POST">
            <div class="card-body">@csrf @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="row g-3">
                    <div class="col-md-4"><label>No Bukti</label><input class="form-control" name="nomor_bukti" required
                            value="{{ old('nomor_bukti', $order?->nomor_bukti) }}"></div>
                    <div class="col-md-4"><label>Batas Transfer</label><input class="form-control" type="date"
                            name="tanggal_batas_transfer" required
                            value="{{ old('tanggal_batas_transfer', $order?->tanggal_batas_transfer?->format('Y-m-d') ?? now()->toDateString()) }}">
                    </div>
                    <div class="col-md-4"><label>Metode</label><select class="form-select" name="metode_bayar">
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="tunai">Tunai</option>
                        </select></div>
                    <div class="col-md-12"><label>Keterangan</label>
                        <textarea class="form-control" name="keterangan">{{ old('keterangan', $order?->keterangan) }}</textarea>
                    </div>
                </div>
                <hr><button class="btn btn-secondary btn-sm" type="button" id="add-item">Tambah Faktur</button>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Faktur</th>
                            <th>Rekening Pemasok</th>
                            <th>Bayar</th>
                            <th>Diskon</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="items"></tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@push('js')
    <script>
        const invoices = @json($invoices),
            banks = @json($supplierBanks),
            items = @json(old('items', $items));
        let n = 0;

        function row(i = {}) {
            let k = n++;
            $('#items').append(
                `<tr><td><select class="form-select" name="items[${k}][faktur_pembelian_id]" required><option></option>${invoices.map(x=>`<option value="${x.id}" ${x.id==i.faktur_pembelian_id?'selected':''}>${x.nomor_form}</option>`).join('')}</select></td><td><select class="form-select" name="items[${k}][rekening_bank_pemasok_id]"><option></option>${banks.map(x=>`<option value="${x.id}" ${x.id==i.rekening_bank_pemasok_id?'selected':''}>${x.nama_bank}</option>`).join('')}</select></td><td><input class="form-control" type="number" min="0.01" step="0.01" name="items[${k}][jumlah_bayar]" value="${i.jumlah_bayar??''}" required></td><td><input class="form-control" type="number" min="0" step="0.01" name="items[${k}][jumlah_diskon]" value="${i.jumlah_diskon??0}"></td><td><button type="button" class="btn btn-danger btn-sm remove" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td></tr>`
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
