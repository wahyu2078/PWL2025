<form action="{{ url('/user/import_ajax') }}" method="POST" id="form-import-user" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-lg" role="document" id="modal-master">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Import Data User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label>Download Template</label><br>
                    <a href="{{ asset('template_user.xlsx') }}" class="btn btn-info btn-sm" download>
                        <i class="fa fa-file-excel-o"></i> Download Template
                    </a>
                </div>

                <div class="form-group">
                    <label>Pilih File Excel</label>
                    <input type="file" name="file_user" class="form-control" required>
                    <span class="text-danger small" id="error-file_user"></span>
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
    $("#form-import-user").on("submit", function(e) {
        e.preventDefault();
        let form = this;
        let formData = new FormData(form);

        $.ajax({
            url: form.action,
            method: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    if (typeof dataUser !== 'undefined') {
                        dataUser.ajax.reload();
                    }
                } else {
                    Swal.fire('Gagal', res.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                console.error(xhr.responseText);
            }
        });
    });
</script>
