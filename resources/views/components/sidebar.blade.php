@push('styles_1')
    <style>
        .nav-link {
            display: flex;
            align-items: center;
        }

        th {
            white-space: nowrap;
        }

        .nav-treeview {
            display: none;
        }

        .menu-open .nav-treeview {
            display: block;
        }

        .scrollable-sidebar {
            height: auto;
            overflow-y: auto;
        }

        .nav-treeview .nav-treeview .nav-link.active {
            background-color: #4CAF50 !important;
            /* Replace with your desired background color */
            color: #ffffff !important;
            /* Optional: Adjust text color for better contrast */
        }

        .sidebar-mini.sidebar-collapse .collapse-user-panel .user-info-text {
            display: none !important;
        }

        .sidebar-mini.sidebar-collapse:hover .collapse-user-panel .user-info-text {
            display: block !important;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }

        /* Sembunyikan input pencarian saat sidebar-collapse */
        .sidebar-mini.sidebar-collapse .input-group .form-control {
            display: none;
        }

        /* Tampilkan kembali input pencarian saat sidebar di-expand */
        .input-group .form-control {
            display: block;
        }

        @media (max-width: 768px) {
            .sidebar {
                height: auto;
                overflow-y: auto;
            }
        }
    </style>
@endpush

<a class="brand-link ms-0 ps-0 pt-3 d-flex w-100" style="text-decoration: none">
    <img class="brand-image elevation-3 img-circle" src="/favicon.ico" alt="Abipraya Logo" style="opacity:.8">
    <span class="brand-text font-weight-light w-100 text-center">PT. Brantas Abipraya</span>
</a>

@auth
    <div class="user-panel ms-0 ps-0 py-3 d-flex collapse-user-panel justify-content-start align-items-center">
        <div class="image ps-3 me-2">
            <img class="img-circle elevation-2" src="{{ Auth::user()->path_profile ?? 'dist/img/user2-160x160.jpg' }}" alt="User Image" style="border-radius: 50%; width: 40px; height: 40px; border: 2pt solid #fff;">
        </div>
        <div class="info text-center w-100 p-0 m-0 me-2">
            <a class="d-block ps-2 pb-1 user-info-text text-white truncate-text p-0 m-0" href="#" style="text-decoration: none">
                <span class="user-text-content m-0 p-0">{{ Auth::user()->name }}</span>
                <br class="p-0 m-0">
                @if (Auth::user()->role === 'superadmin')
                    <span class="user-text-content m-0 px-1 py-1 badge bg-danger rounded-1 fw-normal">SuperAdmin</span>
                @elseif (Auth::user()->role === 'svp')
                    <span class="user-text-content m-0 px-1 py-1 badge bg-primary rounded-1 fw-normal">Senior Vice President</span>
                @elseif (Auth::user()->role === 'vp')
                    <span class="user-text-content m-0 px-1 py-1 badge bg-success rounded-1 fw-normal">Vice President</span>
                @elseif (Auth::user()->role === 'admin_divisi')
                    <span class="user-text-content m-0 px-1 py-1 badge bg-warning text-dark rounded-1 fw-normal">Admin Divisi</span>
                @elseif (Auth::user()->role === 'koordinator_proyek')
                    <span class="user-text-content m-0 px-1 py-1 badge bg-info text-dark rounded-1 fw-normal">Koordinator Proyek</span>
                @endif
            </a>
        </div>
    </div>

    <div class="form-inline mt-0 mx-0 d-flex justify-content-center">
        <div class="input-group w-100">
            <input class="form-control rounded-0 w-75" id="sidebarSearchProyek" aria-label="Search" placeholder="Search">
            <button class="btn btn-sidebar w-25 rounded-0" id="sidebarSearchButton">
                <i class="fas fa-fw fa-search" id="sidebarSearchIcon"></i>
            </button>
        </div>
    </div>

@endauth

<div class="sidebar m-0 p-0">
    <div class="scrollable-sidebar p-0 m-0">
        <div class="os-host os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-scrollbar-horizontal-hidden os-host-transition os-theme-light sidebar">
            <div class="observed os-resize-observer-host">
                <div class="os-resize-observer" style="left:0;right:auto"></div>
            </div>
            <div class="observed os-size-auto-observer" style="height:calc(100% + 1px);float:left">
                <div class="os-resize-observer"></div>
            </div>
            <div class="os-content-glue" style="margin:0 -8px;width:249px;height:866px"></div>
            <div class="os-padding mb-5" style="background-color: #343a40">
                <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y:scroll">
                    <div class="os-content" style="padding:0 8px;height:100%;width:100%">
                        <nav class="mt-2">
                            <ul class="nav nav-pills nav-sidebar flex-column" data-accordion="false" data-widget="treeview" role="menu">

                                <li class="nav-item">
                                    <a class="nav-link {{ $page == 'Dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}" style="{{ $headerPage == 'Dashboard' ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-house-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Dashboard</span>
                                        </p>
                                    </a>
                                </li>

                                <!-- Laporan Semua Proyek Menu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'Laporan Semua Proyek') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'Laporan Semua Proyek') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'Laporan Semua Proyek') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-file-earmark-bar-graph"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Laporan Semua Proyek</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ $page === 'LNPB Total' && $headerPage === 'Laporan Semua Proyek' ? 'active' : '' }}" href="{{ route('laporan.semua.total') }}" style="{{ $page === 'LNPB Total' && $headerPage === 'Laporan Semua Proyek' ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-graph-up"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">LNPB Total</span>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ $page === 'LNPB Bulan Berjalan' && str_contains($headerPage, 'Laporan Semua Proyek') ? 'active' : '' }}" href="{{ route('laporan.semua.bulanberjalan') }}" style="{{ $page === 'LNPB Bulan Berjalan' && str_contains($headerPage, 'Laporan Semua Proyek') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-calendar-check"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">LNPB Bulan Berjalan</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Master Data Menu with Submenu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'Master Data') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'Master Data') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'Master Data') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-database-fill-gear"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Master Data</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">

                                        <!-- Level 2 Item: Data Alat -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($headerPage, 'Master Data') && str_contains($page, 'Data Alat') ? 'active' : '' }}" href="{{ route('master_data_alat.index') }}" style="{{ str_contains($headerPage, 'Master Data') && str_contains($page, 'Data Alat') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-gear-wide-connected"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Alat</span>
                                                </p>
                                            </a>
                                        </li>

                                        <!-- Level 2 Item: Data Sparepart -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Data Sparepart') ? 'active' : '' }}" href="{{ route('master_data_sparepart.index') }}" style="{{ str_contains($page, 'Data Sparepart') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Sparepart</span>
                                                </p>
                                            </a>
                                        </li>

                                        <!-- Level 2 Item: Data Supplier -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Data Supplier') ? 'active' : '' }}" href="{{ route('master_data_supplier.index') }}" style="{{ str_contains($page, 'Data Supplier') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-truck"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Supplier</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- RKB Menu with SubMenu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'RKB') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'RKB') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'RKB') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-building-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">RKB</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <!-- General -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'RKB General') ? 'active' : '' }}" href="{{ route('rkb_general.index') }}" style="{{ str_contains($page, 'Data RKB General') || str_contains($page, 'Detail RKB General') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi bi-plus-square-fill"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">General</span>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- Urgent -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ ($menuContext ?? '') === 'rkb_urgent' ? 'active' : '' }}" href="{{ route('rkb_urgent.index') }}" style="{{ ($menuContext ?? '') === 'rkb_urgent' ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi bi-exclamation-square-fill"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Urgent</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Evaluasi Menu with SubMenu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'Evaluasi') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'Evaluasi') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'Evaluasi') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-bar-chart-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Evaluasi</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <!-- General -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Evaluasi General') ? 'active' : '' }}" href="{{ route('evaluasi_rkb_general.index') }}" style="{{ str_contains($page, 'Evaluasi General') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-clipboard-check-fill"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">General</span>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- Urgent -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ ($menuContext ?? '') === 'evaluasi_urgent' ? 'active' : '' }}" href="{{ route('evaluasi_rkb_urgent.index') }}" style="{{ ($menuContext ?? '') === 'evaluasi_urgent' ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-alarm-fill"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Urgent</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- SPB Menu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'SPB') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'SPB') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'SPB') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-file-earmark-text-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">SPB</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <!-- Level 2 Item: SPB Supplier -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Data SPB Supplier') ? 'active' : '' }}" href="{{ route('spb.index') }}" style="{{ str_contains($headerPage, 'SPB Supplier') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-shop"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Supplier</span>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- Level 2 Item: SPB Proyek -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Data SPB Proyek') ? 'active' : '' }}" href="{{ route('spb.proyek.index') }}" style="{{ str_contains($headerPage, 'SPB Proyek') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-buildings"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Proyek</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Menu for SuperAdmin Role Only -->
                                @if (Auth::user()->role === 'superadmin')
                                    <li class="nav-item">
                                        <a class="nav-link {{ $page === 'Data Proyek' ? 'active' : '' }}" href="{{ route('proyek.index') }}" style="{{ $headerPage === 'Proyek' ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                            <i class="bi me-2 nav-icon bi-kanban"></i>
                                            <p class="truncate-text">
                                                <span class="text-content">Proyek</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ str_contains($page, 'Data User') ? 'active' : '' }}" href="{{ route('users') }}" style="{{ str_contains($headerPage, 'User') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                            <i class="bi me-2 nav-icon bi-people-fill"></i>
                                            <p class="truncate-text">
                                                <span class="text-content">User</span>
                                            </p>
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-header mt-2">LIST PROYEK: {{ $proyeks->count() }}</li>

                                <!-- Dynamic Project Menus -->
                                @foreach ($proyeks as $item)
                                    <li class="nav-item proyek-item has-treeview {{ isset($proyek) && $proyek->id == $item->id ? 'menu-open' : '' }}">
                                        <!-- Level 1 -->
                                        <a class="nav-link {{ isset($proyek) && $proyek->id == $item->id ? 'active' : '' }}" href="#" style="{{ isset($proyek) && $proyek->id == $item->id ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                            <i class="bi me-2 nav-icon fs-5 bi-briefcase-fill"></i>
                                            <p class="truncate-text">
                                                <span class="text-content">{{ $item->nama }} - {{ $item->id }}</span>
                                            </p>
                                            <i class="right bi bi-caret-right-fill"></i>
                                        </a>
                                        <ul class="nav nav-treeview">

                                            <!-- Alat Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ isset($proyek) && str_contains($page, 'Data Alat') && str_contains($headerPage, 'Data Alat') && $proyek->id == $item->id ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ isset($proyek) && str_contains($page, 'Data Alat') && str_contains($headerPage, 'Data Alat') && $proyek->id == $item->id ? 'active' : '' }}" href="{{ route('alat.index', ['id_proyek' => $item->id]) }}" style="{{ isset($proyek) && str_contains($page, 'Data Alat') && str_contains($headerPage, 'Data Alat') && $proyek->id == $item->id ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-wrench"></i>
                                                    <p>Alat</p>
                                                </a>
                                            </li>

                                            <!-- ATB Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'ATB') && str_contains($headerPage, $item->nama) ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ str_contains($page, 'ATB') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="#" style="{{ str_contains($page, 'ATB') && str_contains($headerPage, $item->nama) ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-clipboard-check"></i>
                                                    <p>ATB</p>
                                                    <i class="right bi bi-caret-right-fill"></i>
                                                </a>
                                                <ul class="nav nav-treeview">

                                                    <!-- Level 3 Submenu Items under ATB -->
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data ATB Hutang Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.hutang_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data ATB Hutang Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                            <p>Hutang Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data ATB Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data ATB Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data ATB Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.mutasi_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data ATB Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>Mutasi Proyek</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data ATB Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.panjar_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data ATB Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-cash-stack"></i>
                                                            <p>Panjar Proyek</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- APB Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'APB') && str_contains($headerPage, $item->nama) ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ str_contains($page, 'APB') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="#" style="{{ str_contains($page, 'APB') && str_contains($headerPage, $item->nama) ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-file-earmark-spreadsheet"></i>
                                                    <p>APB</p>
                                                    <i class="right bi bi-caret-right-fill"></i>
                                                </a>
                                                <ul class="nav nav-treeview">

                                                    <!-- Level 3 Submenu Items under APB -->
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb.hutang_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                            <p>EX Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb.panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>EX Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb.mutasi_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>EX Mutasi Proyek</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb.panjar_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-cash-stack"></i>
                                                            <p>EX Panjar Proyek</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- Saldo Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'Saldo') && str_contains($headerPage, $item->nama) ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ str_contains($page, 'Data Saldo') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="#" style="{{ str_contains($page, 'Data Saldo') && str_contains($headerPage, $item->nama) ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-wallet2"></i>
                                                    <p>Saldo <i class="bi bi-caret-right-fill right"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.hutang_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                            <p>EX Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>EX Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.mutasi_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>EX Mutasi Proyek</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.panjar_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-cash-stack"></i>
                                                            <p>EX Panjar Proyek</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- LNPB Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ isset($proyek) && $proyek->id == $item->id && (str_contains($page, 'LNPB') || str_contains($page, 'Summary')) ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ isset($proyek) && $proyek->id == $item->id && (str_contains($page, 'LNPB') || str_contains($page, 'Summary')) ? 'active' : '' }}" href="#" style="{{ isset($proyek) && $proyek->id == $item->id && (str_contains($page, 'LNPB') || str_contains($page, 'Summary')) ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-journal-text"></i>
                                                    <p>LNPB</p>
                                                    <i class="right bi bi-caret-right-fill"></i>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <!-- Total Submenu (Level 3) -->
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ isset($proyek) && $proyek->id == $item->id && $page === 'LNPB Total' ? 'active' : '' }}" href="{{ route('laporan.lnpb.total.index', ['id_proyek' => $item->id]) }}" style="{{ isset($proyek) && $proyek->id == $item->id && $page === 'LNPB Total' ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-graph-up"></i>
                                                            <p>Total</p>
                                                        </a>
                                                    </li>
                                                    <!-- Bulan Berjalan Submenu (Level 3) -->
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ isset($proyek) && $proyek->id == $item->id && $page === 'LNPB Bulan Berjalan' ? 'active' : '' }}" href="{{ route('laporan.lnpb.bulan_berjalan.index', ['id_proyek' => $item->id]) }}" style="{{ isset($proyek) && $proyek->id == $item->id && $page === 'LNPB Bulan Berjalan' ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-check"></i>
                                                            <p>Bulan Berjalan</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                        </ul>
                                    </li>
                                @endforeach

                                <div class="mb-2">
                                    <p></p>
                                </div>
                                <div class="mb-5">
                                    <p></p>
                                </div>
                            </ul>

                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts_1')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/locale/id.min.js" integrity="sha512-he8U4ic6kf3kustvJfiERUpojM8barHoz0WYpAUDWQVn61efpm3aVAD8RWL8OloaDDzMZ1gZiubF9OSdYBqHfQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        moment().format();
        moment.locale('id');
    </script>

    <script>
        $(document).ready(function() {

            function saveSidebarState(isCollapsed) {
                localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
            }

            function loadSidebarState() {
                const savedState = localStorage.getItem('sidebarState');
                if (savedState === 'collapsed') {
                    $('body').addClass('sidebar-collapse');
                } else {
                    $('body').removeClass('sidebar-collapse');
                }
                // Update UI based on loaded state
                toggleUserPanelText($('body').hasClass('sidebar-collapse'));
                toggleSearchInput($('body').hasClass('sidebar-collapse'));
            }

            function toProperCase(text) {
                return text.replace(/\w\S*/g, function(word) {
                    return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
                });
            }

            function initializeTooltip() {
                const truncateLength = 13;
                const userTextTruncateLength = 21; // New specific length for user text

                // Handle user text content specifically
                $(".user-text-content").each(function() {
                    let text = $(this).text().trim();
                    if (text.length > userTextTruncateLength) {
                        const truncatedText = text.substring(0, userTextTruncateLength) + '...';
                        $(this).text(truncatedText);
                        $(this).attr('data-bs-toggle', 'tooltip');
                        $(this).attr('title', text);
                    } else {
                        $(this).text(text);
                    }
                });

                // Handle other truncate text elements
                $(".truncate-text .text-content").each(function() {
                    let text = $(this).text().trim();
                    if (text.length > truncateLength) {
                        const truncatedText = text.substring(0, truncateLength) + '...';
                        $(this).text(truncatedText);
                        $(this).attr('data-bs-toggle', 'tooltip');
                        $(this).attr('title', text);
                    } else {
                        $(this).text(text);
                    }
                });

                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            function handleNavLinkClick() {
                $('.nav-link').on('click', function(event) {
                    let $nextElement = $(this).next('.nav-treeview');
                    if ($nextElement.length) {
                        event.preventDefault();
                        $nextElement.toggle();
                        $(this).parent().toggleClass('menu-open');
                        event.stopPropagation();
                    }
                });
            }

            function handleSidebarBehavior() {
                const sidebar = document.querySelector('.main-sidebar');

                sidebar.addEventListener('mouseenter', function() {
                    toggleUserPanelText(false);
                    toggleSearchInput(false);
                });

                sidebar.addEventListener('mouseleave', function() {
                    toggleUserPanelText($('body').hasClass('sidebar-collapse'));
                    toggleSearchInput($('body').hasClass('sidebar-collapse'));
                });
            }

            function toggleUserPanelText(isCollapsed) {
                const userPanel = document.querySelector('.collapse-user-panel .user-info-text');
                const caretIcons = document.querySelectorAll('.nav-link .bi-caret-right-fill');

                if (isCollapsed) {
                    userPanel.style.display = 'none';
                    userPanel.style.opacity = '0';
                    userPanel.style.visibility = 'hidden';
                    caretIcons.forEach(icon => icon.style.display = 'none');
                    $('.nav-link.level-1').removeClass('ps-4');
                    $('.nav-link.level-2').removeClass('ps-5');
                } else {
                    userPanel.style.display = 'block';
                    userPanel.style.opacity = '1';
                    userPanel.style.visibility = 'visible';
                    caretIcons.forEach(icon => icon.style.display = 'inline');
                    $('.nav-link.level-1').addClass('ps-4');
                    $('.nav-link.level-2').addClass('ps-5');
                }
            }

            function handleSidebarToggle() {
                // When sidebar is collapsed
                $(document).on('collapsed.lte.pushmenu', function() {
                    toggleUserPanelText(true);
                    toggleSearchInput(true);
                    saveSidebarState(true);
                });

                // When sidebar is expanded
                $(document).on('shown.lte.pushmenu', function() {
                    toggleUserPanelText(false);
                    toggleSearchInput(false);
                    saveSidebarState(false);
                });

                // Load initial state
                loadSidebarState();
            }

            function toggleSearchInput(isCollapsed) {
                // Use specific IDs for sidebar elements
                const searchInput = $('#sidebarSearchProyek');
                const searchButton = $('#sidebarSearchButton');

                if (isCollapsed) {
                    searchInput.hide();
                    searchButton.addClass('w-100');
                } else {
                    searchInput.show();
                    searchButton.removeClass('w-100');
                }
            }

            function handleSearchInput() {
                const searchKey = 'sidebarSearchProyek';

                // Load saved search value on page load
                const savedSearch = localStorage.getItem(searchKey);
                if (savedSearch) {
                    $('#sidebarSearchProyek').val(savedSearch);
                    // Trigger search with saved value
                    filterProyekItems(savedSearch);
                }

                // Handle search input
                $('#sidebarSearchProyek').on('keyup', function() {
                    const searchText = $(this).val().toLowerCase();
                    // Save to localStorage
                    localStorage.setItem(searchKey, searchText);
                    filterProyekItems(searchText);
                });

                // Handle clear search
                $('#sidebarSearchButton').on('click', function() {
                    var searchButtonIcon = $('#sidebarSearchIcon');

                    if (searchButtonIcon.hasClass('fa-times')) {
                        $('#sidebarSearchProyek').val('');
                        // Clear from localStorage
                        localStorage.removeItem(searchKey);
                        $('.proyek-item').show();
                        toggleSearchIcon();
                    }
                });

                toggleSearchIcon();
            }

            function filterProyekItems(searchText) {
                $('.proyek-item').each(function() {
                    var proyekTitle = $(this).find('.text-content').attr('data-bs-original-title') || '';
                    proyekTitle = proyekTitle.toLowerCase();

                    if (proyekTitle.indexOf(searchText) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                toggleSearchIcon();
            }

            function toggleSearchIcon() {
                // Use specific IDs for sidebar elements
                var searchText = $('#sidebarSearchProyek').val().trim();
                var searchButton = $('#sidebarSearchIcon');

                if (searchText.length > 0) {
                    searchButton.removeClass('fa-search').addClass('fa-times');
                    $('.sidebar-search-open').addClass('has-text');
                } else {
                    searchButton.removeClass('fa-times').addClass('fa-search');
                    $('.sidebar-search-open').removeClass('has-text');
                }
            }

            function scrollToActiveElement() {
                // Wait for DOM to be fully loaded and elements to be visible
                setTimeout(() => {
                    // Find active links and their parent menu items
                    const activeLinks = $('.sidebar .nav-link.active');
                    const activeMenuItems = $('.sidebar .nav-item.menu-open');

                    // Find the deepest active element
                    let targetElement = null;
                    let maxDepth = -1;

                    // Check active links
                    activeLinks.each(function() {
                        const depth = $(this).parents('.nav-treeview').length;
                        if (depth > maxDepth) {
                            maxDepth = depth;
                            targetElement = $(this);
                        }
                    });

                    // Check active menu items if no active link was found at deeper level
                    if (!targetElement) {
                        activeMenuItems.each(function() {
                            const depth = $(this).parents('.nav-treeview').length;
                            if (depth > maxDepth) {
                                maxDepth = depth;
                                targetElement = $(this);
                            }
                        });
                    }

                    // If we found a target element, scroll to it
                    if (targetElement && targetElement.length) {
                        const sidebarViewport = $('.sidebar .os-viewport');
                        const halfViewportHeight = sidebarViewport.height() / 2;

                        // Ensure parent menus are expanded
                        targetElement.parents('.nav-treeview').show();
                        targetElement.parents('.nav-item').addClass('menu-open');

                        // Calculate scroll position
                        const scrollTo = targetElement.offset().top -
                            sidebarViewport.offset().top +
                            sidebarViewport.scrollTop() -
                            halfViewportHeight;

                        // Smooth scroll
                        sidebarViewport.animate({
                            scrollTop: scrollTo
                        }, {
                            duration: 200,
                            complete: function() {
                                // Ensure element is still visible after animation
                                const finalPosition = targetElement.offset().top;
                                const viewportTop = sidebarViewport.offset().top;
                                const viewportBottom = viewportTop + sidebarViewport.height();

                                if (finalPosition < viewportTop || finalPosition > viewportBottom) {
                                    sidebarViewport.scrollTop(scrollTo);
                                }
                            }
                        });
                    }
                }, 100); // Small delay to ensure DOM is ready
            }

            function handleScrollToActiveElement() {
                scrollToActiveElement();
                $(document).on('shown.lte.pushmenu', scrollToActiveElement);
            }

            function handleWindowResize() {
                const updateSidebarForWidth = () => {
                    const documentWidth = document.documentElement.clientWidth;
                    if (documentWidth < 768) {
                        document.querySelector('body').classList.add('sidebar-collapse');
                        saveSidebarState(true);
                    } else {
                        // Only restore the saved state if screen is large enough
                        loadSidebarState();
                    }
                };

                window.addEventListener('resize', updateSidebarForWidth);
                window.addEventListener('load', updateSidebarForWidth);
            }

            // Initialize functions
            initializeTooltip();
            handleNavLinkClick();
            handleSidebarBehavior();
            handleSidebarToggle();
            handleSearchInput();
            handleScrollToActiveElement();
            handleWindowResize();
            loadSidebarState();
        });
    </script>
@endpush
