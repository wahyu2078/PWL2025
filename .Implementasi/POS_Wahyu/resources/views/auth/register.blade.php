<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Pengguna | POSWahyu</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700&display=fallback" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/icheck-bootstrap/icheck-bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" id="logo-poswahyu" class="h1"><b>POS</b>Wahyu</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Registrasi Pengguna Baru</p>

                <form id="form-register" method="POST" action="{{ url('register') }}">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fas fa-user"></i></div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="error-username"></small>

                    <div class="input-group mb-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="error-nama"></small>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fas fa-lock"></i></div>
                        </div>
                    </div>
                    <small class="text-danger error-text" id="error-password"></small>

                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Konfirmasi Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><i class="fas fa-lock"></i></div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <select name="level_id" class="form-control">
                            <option value="">-- Pilih Level Pengguna --</option>
                            @foreach ($level as $l)
                            <option value="{{ $l->level_id }}">{{ $l->level_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <small class="text-danger error-text" id="error-level_id"></small>

                    <button type="submit" class="btn btn-primary btn-block">Daftar</button>

                    <div class="mt-3 text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-block">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#logo-poswahyu').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Akses Ditolak',
                text: 'Silakan login atau daftar terlebih dahulu untuk mengakses halaman utama.'
            });
        });

        $('#form-register').on('submit', function(e) {
            e.preventDefault();

            $('.error-text').text('');

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(res) {
                    if (res.status === true) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registrasi Berhasil',
                            text: res.message
                        }).then(() => {
                            window.location.href = res.redirect;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: res.message
                        });

                        if (res.msgField) {
                            $.each(res.msgField, function(key, val) {
                                $('#error-' + key).text(val[0]);
                            });
                        }
                    }
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat mengirim data.'
                    });
                }
            });
        });
    </script>

</body>

</html>