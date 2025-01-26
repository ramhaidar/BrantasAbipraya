<nav class="main-header navbar navbar-expand navbar-light navbar-white">
    <ul class="navbar-nav">
        <li class="nav-item">
            {{-- <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="bi bi-menu-button-wide fs-6"></i>
            </a> --}}
            <div class="container text-start">
                <div class="row gx-0">
                    <div class="col">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                            <i class="bi bi-menu-button-wide fs-6"></i>
                        </a>
                    </div>
                    <div class="col-auto">
                        <h5 class="fw-bold m-0 ps-4 pt-2 pb-2">
                            @if (isset($headerPage))
                                {{ $headerPage }}
                            @endif
                        </h5>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <div class="container p-0 m-0 text-center">
                <div class="row gx-0 me-2">
                    {{-- <div class="col-auto">
                        <button class="btn" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                            <i class="bi bi-bell fs-5"></i>
                        </button>
                        <ul class="dropdown-menu container-fluid h-100 w-100" style="left: -250%;">
                            <span class="dropdown-item dropdown-header">15 Notifications</span>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-envelope mr-2"></i> 4 new messages
                                <span class="float-right text-muted text-sm">3 mins</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-users mr-2"></i> 8 friend requests
                                <span class="float-right text-muted text-sm">12 hours</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-file mr-2"></i> 3 new reports
                                <span class="float-right text-muted text-sm">2 days</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item dropdown-footer" href="#">See All Notifications</a>
                        </ul>
                    </div> --}}
                    <div class="col-auto">
                        <button class="bg-transparent border-0 me-2" data-bs-toggle="dropdown" aria-expanded="false">
                            {{-- <i class="bi bi-person fs-5"></i> --}}
                            <img class="img-circle elevation-2" src="{{ Auth::user()->path_profile ?? 'dist/img/user2-160x160.jpg' }}" alt="User Image" style="border-radius: 50%; width: auto; height: 35px; border: 2pt solid #fff;">
                        </button>

                        <!-- Menu Dropdown -->
                        <ul class="dropdown-menu dropdown-menu-lg-start w-100 shadow-lg border-1" style="top: 115%; left: -190%;">
                            <form class="w-100" action="/logout" method="post">
                                @csrf
                                <button class="dropdown-item w-100" type="submit">
                                    <i class="bi bi-box-arrow-left me-2 w-100"></i> Log Out
                                </button>
                            </form>
                        </ul>
                    </div>
                </div>
            </div>
            {{-- <div class="row g-0">
                <div class="col-2">
                    <button class="btn" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                        <i class="far fa-bell"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <span class="dropdown-item dropdown-header">15 Notifications</span>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-envelope mr-2"></i> 4 new messages
                            <span class="float-right text-muted text-sm">3 mins</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-users mr-2"></i> 8 friend requests
                            <span class="float-right text-muted text-sm">12 hours</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-file mr-2"></i> 3 new reports
                            <span class="float-right text-muted text-sm">2 days</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item dropdown-footer" href="#">See All Notifications</a>
                    </ul>
                </div>
                <!-- Tombol Dropdown -->
                <div class="col-2">
                    <button class="btn" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                        <i class="bi bi-person fs-5"></i>
                    </button>

                    <!-- Menu Dropdown -->
                    <ul class="dropdown-menu w-100" style="top: 125%; left: -75%;">
                        <form class="w-100" action="/logout" method="post">
                            @csrf
                            <button class="dropdown-item w-100" type="submit">
                                <i class="bi bi-box-arrow-left me-2 w-100"></i> Log Out
                            </button>
                        </form>
                    </ul>
                </div>
            </div> --}}
        </li>
    </ul>
</nav>
