<form action="{{ url('/supplier/import_ajax') }}" method="POST" id="form-import-supplier" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data Supplier</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label><br>
                    <a href="{{ asset('template_supplier.xlsx') }}" class="btn btn-sm btn-info" download>
                        <i class="fa fa-file-excel-o"></i> Download Template
                    </a>
                </div>

                <div class="form-group">
                    <label for="file_supplier">Pilih File Excel</label>
                    <input type="file" name="file_supplier" id="file_supplier" class="form-control" required>
                    <span class="text-danger small" id="error-file_supplier"></span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</form>
<script>
    $('#form-import-supplier').on('submit', function (e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);

        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function () {
                $(form).find('button[type="submit"]').attr('disabled', true).text('Uploading...');
            },
            success: function (response) {
                if (response.status) {
                    $('#myModal').modal('hide'); // pastikan ID modalnya benar
                    Swal.fire('Berhasil', response.message, 'success');

                    // Reload datatable jika ada
                    if (typeof dataSupplier !== 'undefined') {
                        dataSupplier.ajax.reload(null, false); // false agar tidak reset ke halaman 1
                    }
                } else {
                    Swal.fire('Gagal', response.message || 'Import gagal.', 'error');
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
            },
            complete: function () {
                $(form).find('button[type="submit"]').attr('disabled', false).text('Upload');
            }
        });
    });
</script>
