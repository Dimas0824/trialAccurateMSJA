@php
    $paymentAddress = $paymentAddress ?? (object) [];
    $taxAddress = $taxAddress ?? (object) [];
    $taxProfile = $taxProfile ?? (object) [];
    $contactRows = $contacts->isEmpty() ? collect([(object) []]) : $contacts;
    $bankRows = $bankAccounts->isEmpty() ? collect([(object) []]) : $bankAccounts;
    $balanceRows = $openingBalances->isEmpty() ? collect([(object) []]) : $openingBalances;
    $value = fn ($field, $default = '') => old($field, $supplier->{$field} ?? $default);
    $paymentValue = fn ($field) => old("payment_address.$field", $paymentAddress->{$field} ?? '');
    $taxValue = fn ($field) => old("tax_address.$field", $taxAddress->{$field} ?? '');
    $taxProfileValue = fn ($field, $default = '') => old($field, $taxProfile->{$field} ?? $default);
@endphp

<div class="card shadow-lg mx-4">
    <div class="card-body p-3">
        <div class="row gx-4">
            <div class="col-lg">
                <div class="nav-wrapper">
                    <button class="btn btn-secondary mb-0" type="button" onclick="history.back()"><i class="fas fa-circle-left me-1"></i><span class="font-weight-bold">Kembali</span></button>
                    @if (($method === 'POST' && $authorize->add == '1') || ($method !== 'POST' && $authorize->edit == '1'))
                        <button class="btn btn-primary mb-0" type="button" onclick="event.preventDefault(); document.getElementById('{{ $dmenu }}-form').submit();"><i class="fas fa-floppy-disk me-1"></i><span class="font-weight-bold">Simpan</span></button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-12">
            <form role="form" method="POST" action="{{ $action }}" id="{{ $dmenu }}-form">
                @csrf
                @if ($method !== 'POST')
                    @method($method)
                @endif
                <div class="card">
                    <div class="card-body">
                        <p class="text-uppercase text-sm">{{ $method === 'POST' ? 'Insert' : 'Edit' }} {{ $title_menu }}</p>
                        <hr class="horizontal dark mt-0">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#general" type="button">Umum</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#contacts" type="button">Kontak</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#purchase" type="button">Pembelian</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tax" type="button">Pajak</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#balance" type="button">Saldo Utang</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#other" type="button">Lain-lain</button></li>
                </ul>
                <div class="tab-content pt-4">
                    <div class="tab-pane fade show active" id="general">
                        <div class="row">
                            <div class="col-md-6"><div class="form-group"><label>Nama <span class="text-danger">*</span></label><input class="form-control" name="nama" value="{{ $value('nama') }}" required></div></div>
                            <div class="col-md-4"><div class="form-group"><label>ID Pemasok <span class="text-danger">*</span></label><input class="form-control" name="kode_pemasok" value="{{ $value('kode_pemasok') }}" required></div></div>
                            <div class="col-md-2 d-flex align-items-center"><div class="form-check mt-3"><input class="form-check-input" type="checkbox" name="penomoran_otomatis" value="1" id="automatic-numbering" @checked(old('penomoran_otomatis', $supplier->penomoran_otomatis ?? false))><label class="form-check-label" for="automatic-numbering">Penomoran Otomatis</label></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Format Penomoran</label><select class="form-control custom-select" name="urutan_penomoran_id"><option value="">Pilih format</option>@foreach ($numberFormats as $format)<option value="{{ $format->id }}" @selected($value('urutan_penomoran_id') == $format->id)>{{ $format->nama }}</option>@endforeach</select></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Kategori Pemasok</label><select class="form-control custom-select" name="kategori_pemasok_id"><option value="">Pilih kategori</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected($value('kategori_pemasok_id') == $category->id)>{{ $category->nama }}</option>@endforeach</select></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Tipe Pemasok</label><select class="form-control custom-select" name="tipe_pemasok"><option value="">Pilih tipe</option>@foreach (['perorangan' => 'Perorangan', 'perusahaan' => 'Perusahaan', 'pemerintah' => 'Pemerintah'] as $key => $label)<option value="{{ $key }}" @selected($value('tipe_pemasok') === $key)>{{ $label }}</option>@endforeach</select></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">No. Telp. Bisnis</label><input class="form-control" name="telepon_bisnis" value="{{ $value('telepon_bisnis') }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Handphone</label><input class="form-control" name="nomor_handphone" value="{{ $value('nomor_handphone') }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">No. WhatsApp</label><input class="form-control" name="nomor_whatsapp" value="{{ $value('nomor_whatsapp') }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Email</label><input type="email" class="form-control" name="email" value="{{ $value('email') }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Faksimili</label><input class="form-control" name="nomor_faksimili" value="{{ $value('nomor_faksimili') }}"></div></div>
                            <div class="col-md-6"><div class="form-group"><label class="form-control-label">Website</label><input type="url" class="form-control" name="situs_web" value="{{ $value('situs_web') }}"></div></div>
                            <div class="col-md-12"><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="penjual_jasa_orang_pribadi" value="1" id="individual-service" @checked(old('penjual_jasa_orang_pribadi', $supplier->penjual_jasa_orang_pribadi ?? false))><label class="form-check-label" for="individual-service">Penjual Jasa Orang Pribadi</label></div></div>
                        </div>
                        <p class="text-uppercase text-sm mt-2">Alamat Pembayaran</p>
                        <div class="row">
                            <div class="col-md-12"><div class="form-group"><label>Jalan Pembayaran</label><textarea class="form-control" name="payment_address[jalan]">{{ $paymentValue('jalan') }}</textarea></div></div>
                            @foreach (['kota' => 'Kota', 'kode_pos' => 'Kode Pos', 'provinsi' => 'Provinsi', 'negara' => 'Negara'] as $field => $label)<div class="col-md-6"><div class="form-group"><label class="form-control-label">{{ $label }}</label><input class="form-control" name="payment_address[{{ $field }}]" value="{{ $paymentValue($field) }}"></div></div>@endforeach
                        </div>
                    </div>

                    <div class="tab-pane fade" id="contacts">
                        <div class="d-flex justify-content-between mb-3"><p class="text-uppercase text-sm mb-0">Kontak Pemasok</p><button type="button" class="btn btn-outline-primary btn-sm mb-0" data-add-row="contact-rows">Tambah Kontak</button></div>
                        <div class="table-responsive"><table class="table display" id="contact-rows"><thead class="thead-light" style="background-color: #00b7bd4f;"><tr><th>Nama Lengkap</th><th>Posisi Jabatan</th><th>Email</th><th>Handphone</th><th></th></tr></thead><tbody>@foreach ($contactRows as $index => $contact)<tr><td><input class="form-control" name="contacts[{{ $index }}][nama_lengkap]" value="{{ old("contacts.$index.nama_lengkap", $contact->nama_lengkap ?? '') }}"></td><td><input class="form-control" name="contacts[{{ $index }}][posisi_jabatan]" value="{{ old("contacts.$index.posisi_jabatan", $contact->posisi_jabatan ?? '') }}"></td><td><input type="email" class="form-control" name="contacts[{{ $index }}][email]" value="{{ old("contacts.$index.email", $contact->email ?? '') }}"></td><td><input class="form-control" name="contacts[{{ $index }}][nomor_handphone]" value="{{ old("contacts.$index.nomor_handphone", $contact->nomor_handphone ?? '') }}"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr>@endforeach</tbody></table></div>
                    </div>

                    <div class="tab-pane fade" id="purchase">
                        <div class="row">
                            <div class="col-md-4"><div class="form-group"><label>Syarat Pembayaran</label><select class="form-control custom-select" name="syarat_pembayaran_id"><option value="">Pilih syarat</option>@foreach ($paymentTerms as $term)<option value="{{ $term->id }}" @selected($value('syarat_pembayaran_id') == $term->id)>{{ $term->nama }}</option>@endforeach</select></div></div>
                            <div class="col-md-4"><div class="form-group"><label>Default Diskon (%)</label><input type="number" min="0" step="0.0001" class="form-control" name="diskon_default_persen" value="{{ $value('diskon_default_persen', 0) }}"></div></div>
                            <div class="col-md-12"><div class="form-group"><label>Default Deskripsi</label><textarea class="form-control" name="deskripsi_default">{{ $value('deskripsi_default') }}</textarea></div></div>
                            @foreach (['akun_pembelian_id' => 'Akun Pembelian', 'akun_utang_id' => 'Akun Utang', 'akun_uang_muka_id' => 'Akun Uang Muka'] as $field => $label)<div class="col-md-4"><div class="form-group"><label>{{ $label }}</label><select class="form-control custom-select" name="{{ $field }}"><option value="">Pilih akun</option>@foreach ($accounts as $account)<option value="{{ $account->id }}" @selected($value($field) == $account->id)>{{ $account->kode_akun }} - {{ $account->nama }}</option>@endforeach</select></div></div>@endforeach
                        </div>
                        <div class="d-flex justify-content-between mb-3"><p class="text-uppercase text-sm mb-0">Rekening Bank Pemasok</p><button type="button" class="btn btn-outline-primary btn-sm mb-0" data-add-row="bank-rows">Tambah Rekening</button></div>
                        <div class="table-responsive"><table class="table display" id="bank-rows"><thead class="thead-light" style="background-color: #00b7bd4f;"><tr><th>Nomor Rekening</th><th>Atas Nama</th><th>Nama Bank</th><th></th></tr></thead><tbody>@foreach ($bankRows as $index => $bank)<tr><td><input class="form-control" name="bank_accounts[{{ $index }}][nomor_rekening]" value="{{ old("bank_accounts.$index.nomor_rekening", $bank->nomor_rekening ?? '') }}"></td><td><input class="form-control" name="bank_accounts[{{ $index }}][nama_pemilik_rekening]" value="{{ old("bank_accounts.$index.nama_pemilik_rekening", $bank->nama_pemilik_rekening ?? '') }}"></td><td><input class="form-control" name="bank_accounts[{{ $index }}][nama_bank]" value="{{ old("bank_accounts.$index.nama_bank", $bank->nama_bank ?? '') }}"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr>@endforeach</tbody></table></div>
                    </div>

                    <div class="tab-pane fade" id="tax">
                        <div class="row">
                            <div class="col-md-12"><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="faktur_default_termasuk_pajak" value="1" id="tax-included" @checked(old('faktur_default_termasuk_pajak', $taxProfile->faktur_default_termasuk_pajak ?? false))><label class="form-check-label" for="tax-included">Default Faktur Termasuk Pajak</label></div></div>
                            <div class="col-md-4"><div class="form-group"><label>Tipe ID Pajak</label><input class="form-control" name="tipe_identitas_pajak" value="{{ $taxProfileValue('tipe_identitas_pajak') }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label>Nomor Wajib Pajak</label><input class="form-control" name="nomor_wajib_pajak" value="{{ $taxProfileValue('nomor_wajib_pajak') }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label>Nama Wajib Pajak</label><input class="form-control" name="nama_wajib_pajak" value="{{ $taxProfileValue('nama_wajib_pajak') }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label>NITKU</label><input class="form-control" name="nitku" value="{{ $taxProfileValue('nitku') }}"></div></div>
                            <div class="col-md-4"><div class="form-group"><label>Tipe Transaksi</label><select class="form-control custom-select" name="tipe_transaksi"><option value="">Pilih tipe</option>@foreach (['faktur_pajak' => 'Faktur Pajak', 'impor' => 'Impor', 'perolehan_dalam_negeri' => 'Perolehan Dalam Negeri', 'tidak_dikreditkan' => 'Tidak Dikreditkan', 'ditanggung_pemerintah' => 'Ditanggung Pemerintah'] as $key => $label)<option value="{{ $key }}" @selected($taxProfileValue('tipe_transaksi') === $key)>{{ $label }}</option>@endforeach</select></div></div>
                            <div class="col-md-12"><div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="alamat_pajak_sama_dengan_pembayaran" value="1" id="same-tax-address" @checked(old('alamat_pajak_sama_dengan_pembayaran', $taxProfile->alamat_pajak_sama_dengan_pembayaran ?? true))><label class="form-check-label" for="same-tax-address">Alamat Pajak Sama dengan Alamat Pembayaran</label></div></div>
                        </div>
                        <div class="row" id="tax-address">
                            <div class="col-md-12"><div class="form-group"><label>Jalan Pajak</label><textarea class="form-control" name="tax_address[jalan]">{{ $taxValue('jalan') }}</textarea></div></div>
                            @foreach (['kota' => 'Kota', 'kode_pos' => 'Kode Pos', 'provinsi' => 'Provinsi', 'negara' => 'Negara'] as $field => $label)<div class="col-md-6"><div class="form-group"><label class="form-control-label">{{ $label }}</label><input class="form-control" name="tax_address[{{ $field }}]" value="{{ $taxValue($field) }}"></div></div>@endforeach
                        </div>
                    </div>

                    <div class="tab-pane fade" id="balance">
                        <div class="d-flex justify-content-between mb-3"><p class="text-uppercase text-sm mb-0">Saldo Utang Awal</p><button type="button" class="btn btn-outline-primary btn-sm mb-0" data-add-row="balance-rows">Tambah Saldo</button></div>
                        <div class="table-responsive"><table class="table display" id="balance-rows"><thead class="thead-light" style="background-color: #00b7bd4f;"><tr><th>Tanggal</th><th>Jumlah</th><th>Mata Uang</th><th>Syarat Pembayaran</th><th>Nomor Dokumen</th><th>Keterangan</th><th></th></tr></thead><tbody>@foreach ($balanceRows as $index => $balance)<tr><td><input type="date" class="form-control" name="opening_balances[{{ $index }}][tanggal]" value="{{ old("opening_balances.$index.tanggal", $balance->tanggal ?? '') }}"></td><td><input type="number" min="0" step="0.01" class="form-control" name="opening_balances[{{ $index }}][jumlah]" value="{{ old("opening_balances.$index.jumlah", $balance->jumlah ?? '') }}"></td><td><select class="form-control" name="opening_balances[{{ $index }}][mata_uang_id]"><option value="">Pilih</option>@foreach ($currencies as $currency)<option value="{{ $currency->id }}" @selected(old("opening_balances.$index.mata_uang_id", $balance->mata_uang_id ?? '') == $currency->id)>{{ $currency->kode }}</option>@endforeach</select></td><td><select class="form-control" name="opening_balances[{{ $index }}][syarat_pembayaran_id]"><option value="">Pilih</option>@foreach ($paymentTerms as $term)<option value="{{ $term->id }}" @selected(old("opening_balances.$index.syarat_pembayaran_id", $balance->syarat_pembayaran_id ?? '') == $term->id)>{{ $term->nama }}</option>@endforeach</select></td><td><input class="form-control" name="opening_balances[{{ $index }}][nomor_dokumen]" value="{{ old("opening_balances.$index.nomor_dokumen", $balance->nomor_dokumen ?? '') }}"></td><td><input class="form-control" name="opening_balances[{{ $index }}][keterangan]" value="{{ old("opening_balances.$index.keterangan", $balance->keterangan ?? '') }}"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr>@endforeach</tbody></table></div>
                    </div>

                    <div class="tab-pane fade" id="other">
                        <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="memberikan_nomor_faktur" value="1" id="invoice-number" @checked(old('memberikan_nomor_faktur', $supplier->memberikan_nomor_faktur ?? false))><label class="form-check-label" for="invoice-number">Pemasok Memberikan Nomor Faktur</label></div>
                        <div class="form-group"><label>Catatan</label><textarea class="form-control" name="catatan" rows="5">{{ $value('catatan') }}</textarea></div>
                    </div>
                </div>
                        <hr class="horizontal dark">
                    </div>
                    <div class="card-footer align-items-center pt-0 pb-2"></div>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="contact-row-template"><tr><td><input class="form-control" name="contacts[__INDEX__][nama_lengkap]"></td><td><input class="form-control" name="contacts[__INDEX__][posisi_jabatan]"></td><td><input type="email" class="form-control" name="contacts[__INDEX__][email]"></td><td><input class="form-control" name="contacts[__INDEX__][nomor_handphone]"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr></template>
<template id="bank-row-template"><tr><td><input class="form-control" name="bank_accounts[__INDEX__][nomor_rekening]"></td><td><input class="form-control" name="bank_accounts[__INDEX__][nama_pemilik_rekening]"></td><td><input class="form-control" name="bank_accounts[__INDEX__][nama_bank]"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr></template>
<template id="balance-row-template"><tr><td><input type="date" class="form-control" name="opening_balances[__INDEX__][tanggal]"></td><td><input type="number" min="0" step="0.01" class="form-control" name="opening_balances[__INDEX__][jumlah]"></td><td><select class="form-control" name="opening_balances[__INDEX__][mata_uang_id]"><option value="">Pilih</option>@foreach ($currencies as $currency)<option value="{{ $currency->id }}">{{ $currency->kode }}</option>@endforeach</select></td><td><select class="form-control" name="opening_balances[__INDEX__][syarat_pembayaran_id]"><option value="">Pilih</option>@foreach ($paymentTerms as $term)<option value="{{ $term->id }}">{{ $term->nama }}</option>@endforeach</select></td><td><input class="form-control" name="opening_balances[__INDEX__][nomor_dokumen]"></td><td><input class="form-control" name="opening_balances[__INDEX__][keterangan]"></td><td><button type="button" class="btn btn-sm btn-danger mb-0 remove-row"><i class="fas fa-trash"></i></button></td></tr></template>

@push('js')
    <script>
        const nextRows = { 'contact-rows': {{ $contactRows->count() }}, 'bank-rows': {{ $bankRows->count() }}, 'balance-rows': {{ $balanceRows->count() }} };

        document.querySelectorAll('[data-add-row]').forEach((button) => {
            button.addEventListener('click', () => {
                const tableId = button.dataset.addRow;
                const templateId = tableId.replace(/s$/, '') + '-template';
                const template = document.getElementById(templateId);
                document.querySelector(`#${tableId} tbody`).insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__INDEX__', nextRows[tableId]++));
            });
        });

        document.addEventListener('click', (event) => {
            const button = event.target.closest('.remove-row');
            if (button) button.closest('tr').remove();
        });

        const sameAddress = document.getElementById('same-tax-address');
        const taxAddress = document.getElementById('tax-address');
        const toggleTaxAddress = () => taxAddress.querySelectorAll('input, textarea').forEach((input) => input.disabled = sameAddress.checked);
        sameAddress.addEventListener('change', toggleTaxAddress);
        toggleTaxAddress();
    </script>
@endpush
