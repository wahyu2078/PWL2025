<!-- Main Header -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm">

    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Beranda</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="#" class="nav-link">Kontak</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        <!-- Search -->
        <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button" title="Cari...">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        <input class="form-control form-control-navbar" type="search" placeholder="Cari..." aria-label="Search">
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit"><i class="fas fa-search"></i></button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search"><i class="fas fa-times"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <!-- Messages -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Pesan">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                @foreach ([1, 2, 3] as $i)
                <a href="#" class="dropdown-item">
                    <div class="media">
                        <img src="../../dist/img/user{{ $i }}-128x128.jpg" alt="User Avatar" class="img-size-50 mr-3 img-circle shadow-sm">
                        <div class="media-body">
                            <h3 class="dropdown-item-title mb-1">
                                User {{ $i }}
                                <span class="float-right text-sm text-{{ ['danger','muted','warning'][$i - 1] }}">
                                    <i class="fas fa-star"></i>
                                </span>
                            </h3>
                            <p class="text-sm">Pesan dari user {{ $i }}</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> {{ $i * 2 }} jam lalu</p>
                        </div>
                    </div>
                </a>
                @if($i < 3)
                    <div class="dropdown-divider">
            </div>
            @endif
            @endforeach
            <a href="#" class="dropdown-item dropdown-footer text-center text-primary">Lihat Semua Pesan</a>
            </div>
        </li>

        <!-- Notifications -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" title="Notifikasi">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">15 Notifikasi</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 pesan baru
                    <span class="float-right text-muted text-sm">3 menit</span>
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 permintaan teman
                    <span class="float-right text-muted text-sm">12 jam</span>
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 laporan baru
                    <span class="float-right text-muted text-sm">2 hari</span>
                </a>
                <a href="#" class="dropdown-item dropdown-footer text-center text-primary">Lihat Semua Notifikasi</a>
            </div>
        </li>

        <!-- Fullscreen -->
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Layar Penuh">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        <!-- Control Sidebar -->
        <li class="nav-item">
            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button" title="Sidebar">
                <i class="fas fa-th-large"></i>
            </a>
        </li>
        <li class="nav-item">


            <a href="{{ url('/logout') }}" class="nav-link"


                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">


                <i class="fas fa-sign-out-alt"></i> Logout


            </a>


            <form id="logout-form" action="{{ url('/logout') }}" method="GET" style="display: none;">


                @csrf


            </form>


        </li>
    </ul>
</nav>