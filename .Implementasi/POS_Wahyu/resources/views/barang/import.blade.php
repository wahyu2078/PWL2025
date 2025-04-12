<!-- Modal Import Barang -->
<div class="modal fade" id="modal-master" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ url('/barang/import_ajax') }}" method="POST" id="form-import" enctype="multipart/form-data">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">Import Data Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Tombol Download Template -->
                    <div class="form-group">
                        <label>Download Template</label><br>
                        <a href="{{ asset('template_barang.xlsx') }}" class="btn btn-info btn-sm" download>
                            <i class="fa fa-file-excel"></i> Download Template
                        </a>
                        <small id="error-kategori_id" class="error-text text-danger"></small>
                    </div>

                    <!-- Input File Excel -->
                    <div class="form-group">
                        <label>Pilih File Excel</label>
                        <input type="file" name="file_barang" id="file_barang" class="form-control" required>
                        <small id="error-file_barang" class="error-text text-danger"></small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script jQuery Ajax Import -->
<script>
    $(document).ready(function() {
        $('#modal-master').modal('show');

        $('#form-import').validate({
            rules: {
                file_barang: {
                    required: true,
                    extension: "xlsx"
                }
            },
            messages: {
                file_barang: {
                    required: "File tidak boleh kosong",
                    extension: "Hanya file .xlsx yang diizinkan"
                }
            },
            submitHandler: function(form) {
                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        $('.error-text').text('');
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#modal-master').modal('hide');
                            $('#myModal').modal('hide'); // <<< INI WAJIB supaya backdrop hilang

                            setTimeout(function() {
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            }, 500);

                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            });

                            if (typeof dataBarang !== 'undefined') {
                                dataBarang.ajax.reload(null, false);
                            }
                        } else {
                            $.each(response.msgField, function(prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });

                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });
                        }
                    },

                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Gagal mengunggah file.'
                        });
                    }
                });

                return false;
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });
    });
</script>