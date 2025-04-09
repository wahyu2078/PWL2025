<form action="{{ url('/user/ajax') }}" method="POST" id="form-tambah" enctype="multipart/form-data">
    @csrf
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" class="form-control" required>
                        <option value="">- Pilih Level -</option>
                        @foreach ($level as $l)
                            <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="form-text text-danger error-text"></small>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required>
                    <small id="error-username" class="form-text text-danger error-text"></small>
                </div>

                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                    <small id="error-nama" class="form-text text-danger error-text"></small>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                    <small id="error-password" class="form-text text-danger error-text"></small>
                </div>

                <div class="form-group">
                    <label>Foto Profil (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                    <small id="error-foto" class="form-text text-danger error-text"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $("#form-tambah").on("submit", function (e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);

            $.ajax({
                url: form.action,
                method: form.method,
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('.error-text').text('');
                    if (response.status) {
                        $('#myModal').modal('hide');
                        Swal.fire('Berhasil', response.message, 'success');
                        if (typeof dataUser !== 'undefined') {
                            dataUser.ajax.reload();
                        }
                    } else {
                        $.each(response.msgField, function (prefix, val) {
                            $('#error-' + prefix).text(val[0]);
                        });
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function (xhr) {
                    Swal.fire('Error', 'Terjadi kesalahan pada server.', 'error');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
