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

        .sidebar-mini.sidebar-collapse .collapse-user-panel .user-info-text {
            display: none !important;
        }

        .sidebar-mini.sidebar-collapse:hover .collapse-user-panel .user-info-text {
            display: block !important;
            opacity: 1;
            transition: opacity 0.3s ease-in-out;
        }

        @media (max-width: 768px) {
            .sidebar {
                height: auto;
                overflow-y: auto;
            }
        }
    </style>
@endpush

<a class="brand-link ms-0 ps-0 container-fluid pt-3">
    <img class="brand-image elevation-3 img-circle" src="/favicon.ico" alt="Abipraya Logo" style="opacity:.8">
    <span class="brand-text font-weight-light">PT. Brantas Abipraya</span>
</a>

@auth
    <div class="user-panel ms-0 ps-0 py-2 d-flex collapse-user-panel justify-content-start align-items-center">
        <div class="image me-2">
            <img class="img-circle elevation-2" src="{{ Auth::user()->path_profile ?? 'dist/img/user2-160x160.jpg' }}" alt="User Image" style="border-radius: 50%; width: 40px; height: 40px; border: 2pt solid #fff;">
        </div>
        <div class="info">
            <a class="d-block user-info-text text-white truncate-text" href="#">
                <span class="text-content">{{ Auth::user()->name }} - {{ ucfirst(Auth::user()->role) }}</span>
            </a>
        </div>
    </div>

    <div class="form-inline mt-0 mx-0">
        <div class="input-group" data-widget="sidebar-search">
            <input class="form-control form-control-sidebar" id="searchProyek" type="search" aria-label="Search" placeholder="Search">
            <div class="input-group-append">
                <button class="btn btn-sidebar">
                    <i class="fas fa-fw fa-search"></i>
                </button>
            </div>
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
                                    <a class="nav-link {{ str_contains($page, 'Dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                        <i class="bi me-2 nav-icon fs-5 bi-house-fill"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Dashboard</span>
                                        </p>
                                    </a>
                                </li>

                                <!-- Master Data Menu with Submenu -->
                                <li class="nav-item has-treeview {{ str_contains($headerPage, 'Master Data') ? 'menu-open' : '' }}">
                                    <a class="nav-link {{ str_contains($headerPage, 'Master Data') ? 'active' : '' }}" href="#">
                                        <i class="bi me-2 nav-icon fs-5 bi-database-fill-gear"></i>
                                        <p class="truncate-text">
                                            <span class="text-content">Master Data</span>
                                        </p>
                                        <i class="right bi bi-caret-right-fill"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a class="nav-link {{ str_contains($page, 'Data Alat') ? 'active' : '' }}" href="{{ route('master_data_alat') }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-gear-wide-connected"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Alat</span>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ str_contains($page, 'Data Sparepart') ? 'active' : '' }}" href="{{ route('master_data_sparepart') }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-tools"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Sparepart</span>
                                                </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ str_contains($page, 'Data Supplier') ? 'active' : '' }}" href="{{ route('master_data_supplier') }}">
                                                <i class="bi me-2 nav-icon fs-5 bi-people-fill"></i>
                                                <p class="truncate-text">
                                                    <span class="text-content">Supplier</span>
                                                </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <!-- Menu for Admin Role Only -->
                                @if (Auth::user()->role == 'Admin')
                                    <li class="nav-item">
                                        <a class="nav-link {{ str_contains($page, 'Data Proyek') ? 'active' : '' }}" href="{{ route('proyek') }}">
                                            <i class="bi me-2 nav-icon bi-kanban"></i>
                                            <p class="truncate-text">
                                                <span class="text-content">Proyek</span>
                                            </p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ str_contains($page, 'Data User') ? 'active' : '' }}" href="/users">
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
                                    <li class="nav-item proyek-item has-treeview {{ str_contains($headerPage, $item->nama_proyek) ? 'menu-open' : '' }}">
                                        <a class="nav-link {{ str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="#">
                                            <i class="bi me-2 nav-icon fs-5 bi-briefcase-fill"></i>
                                            <p class="truncate-text">
                                                <span class="text-content">{{ $item->nama_proyek }}</span>
                                            </p>
                                            <i class="right bi bi-caret-right-fill"></i>
                                        </a>
                                        <ul class="nav nav-treeview">

                                            <!-- ATB Submenu -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'ATB') && str_contains($headerPage, $item->nama_proyek) ? 'menu-open' : '' }}">
                                                <a class="nav-link {{ str_contains($page, 'ATB') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="#">
                                                    <i class="bi me-2 nav-icon fs-5 bi-calendar-week-fill"></i>
                                                    <p>
                                                        ATB
                                                        <i class="right bi bi-caret-right-fill"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'Hutang Unit Alat') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('atb_hutang_unit_alat', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-week-fill"></i>
                                                            <p>Hutang Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'Panjar Unit Alat') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('atb_panjar_unit_alat', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-week-fill"></i>
                                                            <p>Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'Mutasi Proyek') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('atb_mutasi_proyek', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-week-fill"></i>
                                                            <p>Mutasi Proyek</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'Panjar Proyek') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('atb_panjar_proyek', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-week-fill"></i>
                                                            <p>Panjar Proyek</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- APB Submenu -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'APB') && str_contains($headerPage, $item->nama_proyek) ? 'menu-open' : '' }}">
                                                <a class="nav-link {{ str_contains($page, 'APB') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="#">
                                                    <i class="bi me-2 nav-icon fs-5 bi-calendar-range-fill"></i>
                                                    <p>
                                                        APB
                                                        <i class="right bi bi-caret-right-fill"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'EX Unit Alat') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('apb_ex_unit_alat', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-range-fill"></i>
                                                            <p>EX Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'EX Panjar Unit Alat') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('apb_ex_panjar_unit_alat', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-range-fill"></i>
                                                            <p>EX Panjar Unit Alat</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'EX Mutasi Saldo') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('apb_ex_mutasi_saldo', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-range-fill"></i>
                                                            <p>EX Mutasi Saldo</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'EX Panjar Proyek') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('apb_ex_panjar_proyek', ['id_proyek' => $item->id]) }}">
                                                            <i class="bi me-2 nav-icon fs-5 bi-calendar-range-fill"></i>
                                                            <p>EX Panjar Proyek</p>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>

                                            <!-- Laporan Submenu -->
                                            <li class="nav-item has-treeview {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama_proyek) ? 'menu-open' : '' }}">
                                                <a class="nav-link {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="#">
                                                    <i class="bi me-2 nav-icon fs-5 bi-newspaper"></i>
                                                    <p>
                                                        Laporan
                                                        <i class="right bi bi-caret-right-fill"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a class="nav-link {{ str_contains($page, 'Summary') && str_contains($headerPage, $item->nama_proyek) ? 'active' : '' }}" href="{{ route('laporan.summary', ['id_proyek' => $item->id]) }}">
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
            <div class="os-scrollbar os-scrollbar-auto-hidden os-scrollbar-horizontal os-scrollbar-unusable">
                <div class="os-scrollbar-track">
                    <div class="os-scrollbar-handle" style="width:100%;transform:translate(0,0)"></div>
                </div>
            </div>
            <div class="os-scrollbar os-scrollbar-auto-hidden os-scrollbar-vertical">
                <div class="os-scrollbar-track">
                    <div class="os-scrollbar-handle" style="height:78.8182%;transform:translate(0,0)">
                    </div>
                </div>
            </div>
            <div class="os-scrollbar-corner"></div>
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
                } else {
                    userPanel.style.display = 'block';
                    userPanel.style.opacity = '1';
                    userPanel.style.visibility = 'visible';

                    // Show all caret icons
                    caretIcons.forEach(icon => icon.style.display = 'inline');
                }
            }

            // Listen for sidebar collapse events
            $(document).on('collapsed.lte.pushmenu', function() {
                toggleUserPanelText(true);
            });

            $(document).on('shown.lte.pushmenu', function() {
                toggleUserPanelText(false);
            });

            // Initial check on page load
            toggleUserPanelText($('body').hasClass('sidebar-collapse'));

            const sidebar = document.querySelector('.main-sidebar');

            sidebar.addEventListener('mouseenter', function() {
                const userPanel = document.querySelector('.collapse-user-panel .user-info-text');
                const caretIcons = document.querySelectorAll('.nav-link .bi-caret-right-fill');

                userPanel.style.display = 'block';
                userPanel.style.opacity = '1';
                userPanel.style.visibility = 'visible';

                // Show all caret icons on hover
                caretIcons.forEach(icon => icon.style.display = 'inline');
            });

            sidebar.addEventListener('mouseleave', function() {
                toggleUserPanelText($('body').hasClass('sidebar-collapse'));
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
            // Temukan elemen dengan kelas `active` dalam sidebar
            const activeElement = document.querySelector('.nav-sidebar .active');

            if (activeElement) {
                // Gulir ke elemen `active` jika ditemukan
                activeElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        });
    </script>

    @yield('script')
@endpush
