<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AdminLTE 3 Sidebar Example with Active Submenu</title>

    <!-- AdminLTE CSS -->
    <link href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet" rel="stylesheet">

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a class="brand-link" href="index3.html">
                <img class="brand-image img-circle elevation-3" src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" style="opacity: .8">
                <span class="brand-text font-weight-light">AdminLTE 3</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" data-accordion="false" role="menu">

                        <!-- Parent Menu Item -->
                        <li class="nav-item has-treeview menu-open"> <!-- Set menu-open to keep this menu open -->
                            <a class="nav-link" href="#">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>

                            <!-- Submenu Level 1 -->
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard v1</p>
                                    </a>
                                </li>

                                <!-- Active Submenu Level 1 Item -->
                                <li class="nav-item has-treeview menu-open"> <!-- Set menu-open to keep this submenu open -->
                                    <a class="nav-link active" href="#"> <!-- Set active to highlight this submenu -->
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>
                                            Dashboard v2
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>

                                    <!-- Active Subsubmenu Level 2 -->
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>Sub Dashboard v2.1</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" href="#"> <!-- Set active to highlight this subsubmenu item -->
                                                <i class="far fa-dot-circle nav-icon"></i>
                                                <p>Sub Dashboard v2.2</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="#">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Dashboard v3</p>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Another Parent Menu Item -->
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Simple Link
                                </p>
                            </a>
                        </li>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <h1>Welcome to AdminLTE 3</h1>
                            <p>This is a sample page to demonstrate the sidebar with active submenus and sub-submenus.</p>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Copyright &copy; 2023 <a href="https://adminlte.io">AdminLTE.io</a>.</strong>
            All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 3.2.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>
