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
        <div class="info text-center w-100 me-2">
            <a class="d-block text-center user-info-text text-white truncate-text" href="#" style="text-decoration: none">
                <span class="text-content">{{ Auth::user()->name }} - {{ ucfirst(Auth::user()->role) }}</span>
            </a>
        </div>
    </div>

    <div class="form-inline mt-0 mx-0 d-flex justify-content-center">
        <div class="input-group w-100">
            <input class="form-control w-75" id="searchProyek" aria-label="Search" placeholder="Search">
            <button class="btn btn-sidebar w-25">
                <i class="fas fa-fw fa-search"></i>
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

                                <!-- Menu for Admin Role Only -->
                                @if (Auth::user()->role == 'Admin')
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

                                <!-- Rancangan Kebutuhan Barang Menu with SubMenu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'RKB') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'RKB') ? 'active' : '' }}" href="#" style="{{ str_contains($headerPage, 'RKB') ? 'background-color: #483D8B; color: #ffffff;' : '' }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-building-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">RKB</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">

                                        <!-- Level 2 Item: Data Alat -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'RKB General') ? 'active' : '' }}" href="{{ route('rkb_general.index') }}" style="{{ str_contains($page, 'Data RKB General') || str_contains($page, 'Detail RKB General') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi bi-plus-square-fill"></i>
                                                {{-- <i class="fa-solid fa-building me-2 fs-5 nav-icon"></i> --}}
                                                <p class="truncate-text">
                                                    <span class="text-content">General</span>
                                                </p>
                                            </a>
                                        </li>

                                        <!-- Level 2 Item: Data Sparepart -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'RKB Urgent') ? 'active' : '' }}" href="{{ route('rkb_urgent.index') }}" style="{{ str_contains($page, 'Data RKB Urgent') || str_contains($page, 'Detail RKB Urgent') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi bi-exclamation-square-fill"></i>
                                                {{-- <i class="fa-solid fa-building-circle-exclamation ms-2 me-2 fs-5 nav-icon"></i> --}}
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
                                        <i class="bi me-2 nav-icon fs-5 bi-bar-chart-fill"></i> <!-- Icon updated to chart icon -->
                                        <p class="truncate-text">
                                            <span class="text-content">Evaluasi</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <!-- Level 2 Item: Evaluasi General -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Evaluasi General') ? 'active' : '' }}" href="{{ route('evaluasi_rkb_general.index') }}" style="{{ str_contains($page, 'Evaluasi General') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-clipboard-check-fill"></i> <!-- Icon for general -->
                                                <p class="truncate-text">
                                                    <span class="text-content">General</span>
                                                </p>
                                            </a>
                                        </li>
                                        <!-- Level 2 Item: Evaluasi Urgent -->
                                        <li class="nav-item">
                                            <a class="nav-link level-1 {{ str_contains($page, 'Timeline Detail RKB Urgent') || str_contains($page, 'Evaluasi Urgent') ? 'active' : '' }}" href="{{ route('evaluasi_rkb_urgent.index') }}" style="{{ str_contains($page, 'Timeline Detail RKB Urgent') || str_contains($page, 'Evaluasi Urgent') ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-alarm-fill"></i> <!-- Icon for urgent -->
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
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Hutang Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.hutang_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Hutang Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                            <p>Hutang Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.mutasi_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Mutasi Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>Mutasi Proyek</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('atb.panjar_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
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
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb_ex_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                            <p>EX Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb_ex_panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>EX Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Mutasi Saldo') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb_ex_mutasi_saldo', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Mutasi Saldo') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>EX Mutasi Saldo</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data APB EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('apb_ex_panjar_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data APB EX Panjar Proyek') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
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
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Mutasi Saldo') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.mutasi_proyek', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Mutasi Saldo') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-arrow-left-right"></i>
                                                            <p>EX Mutasi Saldo</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Data Saldo EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('saldo.panjar_unit_alat', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Data Saldo EX Panjar Unit Alat') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-currency-dollar"></i>
                                                            <p>EX Panjar Unit Alat</p>
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

                                            <!-- Laporan Submenu (Level 2) -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama) ? 'menu-open' : '' }}">
                                                <a class="ps-4 nav-link level-1 {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="#" style="{{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama) ? 'background-color: #66CDAA; color: #ffffff;' : '' }}">
                                                    <i class="bi me-2 nav-icon fs-5 bi-journal-text"></i>
                                                    <p>Laporan</p>
                                                    <i class="right bi bi-caret-right-fill"></i>
                                                </a>
                                                <ul class="nav nav-treeview">

                                                    <!-- Level 3 Submenu Item under Laporan -->
                                                    <li class="nav-item">
                                                        <a class="ps-5 nav-link level-2 {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama) ? 'active' : '' }}" href="{{ route('laporan.summary', ['id_proyek' => $item->id]) }}" style="{{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama) ? 'background-color: #D3D3D3; color: #000000;' : '' }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-newspaper"></i>
                                                            <p>Summary</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                        </ul>
                                    </li>
                                @endforeach

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
            function toProperCase(text) {
                return text.replace(/\w\S*/g, function(word) {
                    return word.charAt(0).toUpperCase() + word.substr(1).toLowerCase();
                });
            }

            const truncateLength = 13;

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
        });

        const eventNavLink = target => {
            const elTarget = document.querySelector(target);
            elTarget.style.display = elTarget.style.display == 'none' ? 'block' : 'none';

            const navLink = elTarget.closest('.nav-item');
            if (!navLink.classList.contains('menu-is-opening')) {
                navLink.classList.add('menu-is-opening', 'menu-open');
            } else {
                navLink.classList.remove('menu-is-opening', 'menu-open');
            }
        };

        window.addEventListener('load', () => {
            const documentWidth = document.documentElement.clientWidth;

            if (documentWidth < 768) {
                document.querySelector('body').classList.add('sidebar-collapse');
            } else {
                // document.querySelector('body').classList.remove('sidebar-collapse');
            }
        });

        window.addEventListener('resize', () => {
            const documentWidth = document.documentElement.clientWidth;
            if (documentWidth < 768) {
                document.querySelector('body').classList.add('sidebar-collapse');
            } else {
                // document.querySelector('body').classList.remove('sidebar-collapse');
            }
        });

        $(document).ready(function() {
            $('.nav-link').on('click', function(event) {
                let $nextElement = $(this).next('.nav-treeview');
                if ($nextElement.length) {
                    event.preventDefault();
                    $nextElement.toggle();
                    $(this).parent().toggleClass('menu-open');
                    event.stopPropagation();
                }
            });
        });

        $(document).ready(function() {

            function toggleUserPanelText(isCollapsed) {
                const userPanel = document.querySelector('.collapse-user-panel .user-info-text');
                const caretIcons = document.querySelectorAll('.nav-link .bi-caret-right-fill'); // Select all caret icons

                if (isCollapsed) {
                    userPanel.style.display = 'none';
                    userPanel.style.opacity = '0';
                    userPanel.style.visibility = 'hidden';

                    // Hide all caret icons
                    caretIcons.forEach(icon => icon.style.display = 'none');

                    // Remove padding classes when sidebar is collapsed
                    $('.nav-link.level-1').removeClass('ps-4');
                    $('.nav-link.level-2').removeClass('ps-5');
                } else {
                    userPanel.style.display = 'block';
                    userPanel.style.opacity = '1';
                    userPanel.style.visibility = 'visible';

                    // Show all caret icons
                    caretIcons.forEach(icon => icon.style.display = 'inline');

                    // Add padding classes based on level
                    $('.nav-link.level-1').addClass('ps-4'); // Level 1 padding
                    $('.nav-link.level-2').addClass('ps-5'); // Level 2 padding
                }
            }

            // Listen for sidebar collapse and expand events
            $(document).on('collapsed.lte.pushmenu', function() {
                toggleUserPanelText(true);
            });

            $(document).on('shown.lte.pushmenu', function() {
                toggleUserPanelText(false);
            });

            // Initial check on page load
            toggleUserPanelText($('body').hasClass('sidebar-collapse'));

            // Sidebar mouseover and mouseout behavior
            const sidebar = document.querySelector('.main-sidebar');

            sidebar.addEventListener('mouseenter', function() {
                const userPanel = document.querySelector('.collapse-user-panel .user-info-text');
                const caretIcons = document.querySelectorAll('.nav-link .bi-caret-right-fill');

                userPanel.style.display = 'block';
                userPanel.style.opacity = '1';
                userPanel.style.visibility = 'visible';

                // Show all caret icons on hover
                caretIcons.forEach(icon => icon.style.display = 'inline');

                // Temporarily add padding classes on hover when sidebar is collapsed
                if ($('body').hasClass('sidebar-collapse')) {
                    $('.nav-link.level-1').addClass('ps-4'); // Level 1 padding
                    $('.nav-link.level-2').addClass('ps-5'); // Level 2 padding
                }
            });

            sidebar.addEventListener('mouseleave', function() {
                // Check if sidebar is collapsed and adjust classes accordingly
                if ($('body').hasClass('sidebar-collapse')) {
                    toggleUserPanelText(true); // Hide user panel and caret icons
                    $('.nav-link.level-1').removeClass('ps-4'); // Remove padding for collapsed state
                    $('.nav-link.level-2').removeClass('ps-5'); // Remove padding for collapsed state
                } else {
                    toggleUserPanelText(false);
                }
            });
        });

        $(document).ready(function() {
            // Fungsi untuk menampilkan atau menyembunyikan input pencarian
            function toggleSearchInput(isCollapsed) {
                const searchInput = $('.input-group .form-control');
                const searchButton = $('.input-group .btn-sidebar');

                if (isCollapsed) {
                    searchInput.hide(); // Sembunyikan input pencarian
                    searchButton.addClass('w-100');
                } else {
                    searchInput.show(); // Tampilkan input pencarian
                    searchButton.removeClass('w-100');
                }
            }

            // Panggil toggleSearchInput berdasarkan kondisi sidebar saat halaman dimuat
            toggleSearchInput($('body').hasClass('sidebar-collapse'));

            // Deteksi perubahan sidebar collapse dan expand
            $(document).on('collapsed.lte.pushmenu', function() {
                toggleSearchInput(true); // Sembunyikan input saat sidebar collapse
            });

            $(document).on('shown.lte.pushmenu', function() {
                toggleSearchInput(false); // Tampilkan input saat sidebar expand
            });

            // Tampilkan input pencarian saat mouseover sidebar dalam kondisi collapse
            const sidebar = document.querySelector('.main-sidebar');
            sidebar.addEventListener('mouseenter', function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    toggleSearchInput(false); // Tampilkan input pencarian saat hover
                }
            });

            sidebar.addEventListener('mouseleave', function() {
                if ($('body').hasClass('sidebar-collapse')) {
                    toggleSearchInput(true); // Sembunyikan kembali input pencarian saat mouse keluar
                }
            });
        });


        $(document).ready(function() {
            function toggleSearchIcon() {
                var searchText = $('#searchProyek').val().trim();
                var searchButton = $('.btn.btn-sidebar i');

                if (searchText.length > 0) {

                    searchButton.removeClass('fa-search').addClass('fa-times');
                    $('.sidebar-search-open').addClass('has-text');
                } else {

                    searchButton.removeClass('fa-times').addClass('fa-search');
                    $('.sidebar-search-open').removeClass('has-text');
                }
            }

            $('#searchProyek').on('keyup', function() {
                var searchText = $(this).val().toLowerCase();

                $('.proyek-item').each(function() {

                    var proyekTitle = $(this).find('.text-content').attr(
                        'data-bs-original-title') || '';
                    proyekTitle = proyekTitle.toLowerCase();

                    if (proyekTitle.indexOf(searchText) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                toggleSearchIcon();
            });

            $('.btn.btn-sidebar').on('click', function() {
                var searchButtonIcon = $(this).find('i');

                if (searchButtonIcon.hasClass('fa-times')) {

                    $('#searchProyek').val('');
                    $('.proyek-item').show();
                    toggleSearchIcon();
                }
            });

            toggleSearchIcon();
        });
    </script>

    <script>
        $(document).ready(function() {
            // Auto-scroll to the active element
            function scrollToActiveElement() {
                // Select the active element within the sidebar
                const activeElement = $('.sidebar .nav-link.active').last();

                // Calculate half of the dynamic view height (dvh)
                const halfDvh = window.innerHeight / 2;

                // Check if there is an active element to scroll to
                if (activeElement.length) {
                    // Scroll the sidebar to bring the active element into view
                    $('.sidebar .os-viewport').animate({
                        scrollTop: activeElement.offset().top - $('.sidebar .os-viewport').offset().top + $('.sidebar .os-viewport').scrollTop() - halfDvh
                    }, 200);
                }
            }

            // Call the scroll function on page load
            scrollToActiveElement();

            // Optionally, also call it when toggling between collapsed and expanded views
            $(document).on('shown.lte.pushmenu', scrollToActiveElement);
        });
    </script>

    @yield('script')
@endpush
