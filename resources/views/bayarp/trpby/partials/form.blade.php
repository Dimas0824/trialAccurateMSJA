<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Tambah {{ $title_menu }}</h5>
        </div>
        <div class="card-body p-3 border-bottom">
            <div class="nav-wrapper">
                <a class="btn btn-secondary mb-0" href="{{ url($url_menu) }}">
                    <i class="fas fa-circle-left me-1"></i><span class="font-weight-bold">Kembali</span>
                </a>
                <button class="btn btn-primary mb-0" form="trpby-form" type="submit">
                    <i class="fas fa-floppy-disk me-1"></i><span class="font-weight-bold">Simpan</span>
                </button>
            </div>
        </div>
        <form action="{{ $action }}" id="trpby-form" method="POST">
            <div class="card-body">@csrf <div class="row g-3">
                    <div class="col-md-4"><label>No Bukti</label><input class="form-control" name="nomor_bukti" required
                            value="{{ old('nomor_bukti') }}"></div>
                    <div class="col-md-4"><label>Tanggal</label><input class="form-control" type="date"
                            name="tanggal_pembayaran" required
                            value="{{ old('tanggal_pembayaran', now()->toDateString()) }}"></div>
                    <div class="col-md-4"><label>Metode</label><select class="form-select" name="metode_bayar" required>
                            <option value="transfer_bank">Transfer Bank</option>
                            <option value="tunai">Tunai</option>
                            <option value="cek_giro">Cek/Giro</option>
                        </select></div>
                    <div class="col-md-6"><label>Pemasok</label><select class="form-select" name="pemasok_id" required>
                            <option></option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"><label>Bank</label><select class="form-select" name="akun_bank_id" required>
                            <option></option>
                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->nama_akun }}</option>
                            @endforeach
                        </select></div>
                    <div class="col-md-12"><label>Keterangan</label>
                        <textarea class="form-control" name="keterangan"></textarea>
                    </div>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <h6>Faktur</h6><button class="btn btn-secondary btn-sm" type="button" id="add-item">Tambah
                        Baris</button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Faktur</th>
                                <th>Bayar</th>
                                <th>Diskon</th>
                                <th></th>
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
        const invoices = @json($invoices);
        let n = 0;

        function row() {
            let k = n++;
            $('#items').append(
                `<tr><td><select class="form-select" name="items[${k}][faktur_pembelian_id]" required><option></option>${invoices.map(x=>`<option value="${x.id}">${x.nomor_form} — ${x.jumlah_terutang}</option>`).join('')}</select></td><td><input class="form-control" name="items[${k}][jumlah_bayar]" type="number" min="0.01" step="0.01" required></td><td><input class="form-control" name="items[${k}][jumlah_diskon]" type="number" min="0" step="0.01" value="0"></td><td><button type="button" class="btn btn-danger btn-sm remove" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td></tr>`
                )
        }
        $(function() {
            row();
            $('#add-item').click(row);
            $('#items').on('click', '.remove', function() {
                $(this).closest('tr').remove()
            })
        })
    </script>
@endpush
