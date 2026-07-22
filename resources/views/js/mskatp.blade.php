<script>
    $(function () {
        const subKategori = $('[name="sub_kategori"]');
        const kategoriInduk = $('[name="kategori_induk_id"]').closest('.form-group');

        function toggleKategoriInduk() {
            kategoriInduk.toggle(subKategori.val() === '1');
        }

        subKategori.on('change', toggleKategoriInduk);
        toggleKategoriInduk();
    });
</script>
