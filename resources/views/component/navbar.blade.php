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
                            @yield('header')
                        </h5>
                    </div>
                </div>
            </div>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <div class="btn-group dropstart">
                <button class="btn" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    <i class="far fa-bell"></i>
                    {{-- <span class="badge badge-warning navbar-badge">15</span> --}}
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
                <button class="btn" data-bs-toggle="dropdown" type="button" aria-expanded="false">
                    <i class="bi bi-person fs-5"></i>
                </button>
                <ul class="dropdown-menu">
                    <form action="/logout" method="post">
                        @csrf
                        <button class="dropdown-item" type="submit">
                            <i class="bi bi-box-arrow-left me-2"></i> Log Out
                        </button>
                    </form>
                </ul>
            </div>
        </li>
    </ul>
</nav>
