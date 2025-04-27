@empty($user)
<!-- Jika user tidak ditemukan -->
<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Kesalahan</h5>
            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                Data yang anda cari tidak ditemukan
            </div>
            <a href="{{ url('/user') }}" class="btn btn-warning">Kembali</a>
        </div>
    </div>
</div>
@else
<!-- Edit -->
<form action="{{ url('/user/' . $user->user_id . '/update_ajax') }}" method="POST" id="form-edit" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Data User</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Level -->
                <div class="form-group">
                    <label>Level Pengguna</label>
                    <select name="level_id" class="form-control" required>
                        <option value="">- Pilih Level -</option>
                        @foreach($level as $l)
                            <option value="{{ $l->level_id }}" {{ $l->level_id == $user->level_id ? 'selected' : '' }}>
                                {{ $l->level_nama }}
                            </option>
                        @endforeach
                    </select>
                    <small id="error-level_id" class="text-danger error-text"></small>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="{{ $user->username }}" class="form-control" required>
                    <small id="error-username" class="text-danger error-text"></small>
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" name="nama" value="{{ $user->nama }}" class="form-control" required>
                    <small id="error-nama" class="text-danger error-text"></small>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label>Password (opsional)</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    <small id="error-password" class="text-danger error-text"></small>
                </div>

                <!-- Foto -->
                <div class="form-group">
                    <label>Foto Profil</label><br>
                    @if ($user->foto && file_exists(public_path('uploads/user/' . $user->foto)))
                        <img src="{{ asset('uploads/user/' . $user->foto) }}" alt="Foto Profil" class="img-thumbnail mb-2" width="100">
                    @endif
                    <input type="file" name="foto" class="form-control">
                    <small id="error-foto" class="text-danger error-text"></small>
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
    $("#form-edit").on("submit", function (e) {
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
                $('.error-text').text('');
                if (res.status) {
                    $('#myModal').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success');
                    if (typeof dataUser !== 'undefined') {
                        dataUser.ajax.reload();
                    }
                } else {
                    $.each(res.msgField, function (prefix, val) {
                        $('#error-' + prefix).text(val[0]);
                    });
                    Swal.fire('Gagal', res.message, 'error');
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
@endempty
