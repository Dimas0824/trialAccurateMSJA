<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $transfer ? 'Edit' : 'Tambah' }} {{ $title_menu }}</h5>
        </div>
        <div class="card-body p-3 border-bottom"><div class="nav-wrapper"><a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}"><i class="fas fa-circle-left me-1"></i><span class="font-weight-bold">Kembali</span></a><button class="btn btn-primary mb-0" form="trtrfp-form" type="submit"><i class="fas fa-floppy-disk me-1"></i><span class="font-weight-bold">Simpan</span></button></div></div>
        <form action="{{ $action }}" id="trtrfp-form" method="POST">
            <div class="card-body">@csrf @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="row g-3">
                    <div class="col-md-6"><label>Tanggal Transfer</label><input class="form-control" type="date"
                            name="tanggal_transfer" required
                            value="{{ old('tanggal_transfer', $transfer?->tanggal_transfer?->format('Y-m-d') ?? now()->toDateString()) }}">
                    </div>
                    <div class="col-md-6"><label>Rekening Bank Sumber</label><select class="form-select"
                            name="rekening_bank_perusahaan_id" required>
                            <option></option>
                            @foreach ($companyBanks as $bank)
                                <option value="{{ $bank->id }}" @selected(old('rekening_bank_perusahaan_id', $transfer?->rekening_bank_perusahaan_id) == $bank->id)>{{ $bank->nama_bank }}
                                </option>
                            @endforeach
                        </select></div>
                </div>
                <hr><button class="btn btn-secondary btn-sm" type="button" id="add-item">Tambah Transaksi</button>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transaksi</th>
                            <th>Rekening Pemasok</th>
                            <th>Nilai Bayar</th>
                            <th>Referensi</th>
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
        const tx = @json($transactions),
            banks = @json($supplierBanks),
            items = @json(old('items', $items));
        let n = 0;

        function row(i = {}) {
            let k = n++;
            $('#items').append(
                `<tr><td><select class="form-select" name="items[${k}][rincian_perintah_pembayaran_id]" required><option></option>${tx.map(x=>`<option value="${x.id}" ${x.id==i.rincian_perintah_pembayaran_id?'selected':''}>${x.nomor_bukti} - ${x.nomor_form}</option>`).join('')}</select></td><td><select class="form-select" name="items[${k}][rekening_bank_pemasok_id]" required><option></option>${banks.map(x=>`<option value="${x.id}" ${x.id==i.rekening_bank_pemasok_id?'selected':''}>${x.nama_bank}</option>`).join('')}</select></td><td><input class="form-control" type="number" min="0.01" step="0.01" name="items[${k}][jumlah_transfer]" value="${i.jumlah_transfer??''}" required></td><td><input class="form-control" name="items[${k}][nomor_referensi]" value="${i.nomor_referensi??''}"></td><td><button type="button" class="btn btn-danger btn-sm remove" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td></tr>`
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
