<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel SB Admin 2">
    <meta name="author" content="Alejandro RH">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/2.0.7/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link href="{{ asset('img/favicon.png') }}" rel="icon" type="image/png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('scripts')
</head>
<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center">
            <div class="sidebar-brand-icon rotate-n-1" >
                <img src="{{ asset('img/logo-placeholder.svg') }}" style="width: 50px;">

            </div>
            <div class="sidebar-brand-text mx-3">Company Name</div>
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">
        @if(Auth::check() && Auth::user()->usertype == 'admin')

        <!-- Nav Item - Dashboard -->
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>{{ __('Dashboard') }}</span></a>
        </li>
 <!-- Divider -->
 <hr class="sidebar-divider my-0">




        <!-- Nav Item - Profile -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.users') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>{{ __('Users') }}</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">


        <li class="nav-item ">
            <a class="nav-link" href="{{ route('interns.list') }}">
                <i class="fas fa-fw fa-id-card "></i>
                <span>{{ __('Interships') }}</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">

        <li class="nav-item ">
            <a class="nav-link" href="{{ route('departments.index') }}">
                <i class="fas fa-fw fa-building "></i>
                <span>{{ __('Departments') }}</span>
            </a>
        </li>
        <!-- Nav Item - About --> <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('admin.finalReports') }}">
                <i class="fas fa-fw fa-file"></i>
                <span>{{ __('Reports') }}</span>
            </a>
        </li> <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('admin.assignInternsToSupervisors') }}">
                <i class="fas fa-fw fa-exchange"></i>
                <span>{{ __('Assign Interns') }}</span>
            </a>
        </li>
        <hr class="sidebar-divider my-0">

        @endif
        @if(Auth::check() && Auth::user()->usertype == 'supervisor')

        <!-- Nav Item - Dashboard -->
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('supervisor.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>{{ __('Dashboard') }}</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            {{ __('Settings') }}
        </div>

        <!-- Nav Item - Profile -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('supervisor.tasks') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>{{ __('Tasks') }}</span>
            </a>
        </li>

        <!-- Nav Item - About -->
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('supervisor.weeklyReports') }}">
                <i class="fas fa-fw fa-file"></i>
                <span>{{ __('Reports') }}</span>
            </a>
        </li>
        @endif
        @if(Auth::check() && Auth::user()->usertype == 'intern')

        <!-- Nav Item - Dashboard -->
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('intern.dashboard') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>{{ __('Dashboard') }}</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            {{ __('Settings') }}
        </div>

        <!-- Nav Item - Profile -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('intern.tasks') }}">
                <i class="fas fa-fw fa-tasks"></i>
                <span>{{ __('Tasks') }}</span>
            </a>
        </li>

        <!-- Nav Item - About -->
        <li class="nav-item ">
            <a class="nav-link" href="{{ route('intern.reports') }}">
                <i class="fas fa-fw fa-file"></i>
                <span>{{ __('Reports') }}</span>
            </a>
        </li>

        <li class="nav-item ">
            <a class="nav-link" href="{{ route('intern.attestation') }}">
                <i class="fas fa-fw fa-certificate "></i>
                <span>{{ __('Attestation') }}</span>
            </a>
        </li>
        @endif
        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Topbar -->
            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <!-- Sidebar Toggle (Topbar) -->
                <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                    <i class="fa fa-bars"></i>
                </button>

                <!-- Topbar Search -->
               

                <!-- Topbar Navbar -->
                <ul class="navbar-nav ml-auto">

                    <div class="topbar-divider d-none d-sm-block"></div>

                    <!-- Nav Item - User Information -->
                    <li class="nav-item dropdown no-arrow">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                            <figure class="img-profile rounded-circle avatar font-weight-bold" data-initial="{{ Auth::user()->name[0] }}"></figure>
                        </a>
                        <!-- Dropdown - User Information -->
                        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                            <a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{ __('Profile') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{ __('Settings') }}
                            </a>
                            <a class="dropdown-item" href="javascript:void(0)">
                                <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{ __('Activity Log') }}
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                {{ __('Logout') }}
                            </a>
                        </div>
                    </li>

                </ul>

            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">

                @yield('main-content')

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->



    </div>
    <!-- End of Content Wrapper -->

</div>

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('Ready to Leave?') }}</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-link" type="button" data-dismiss="modal">{{ __('Cancel') }}</button>
                <a class="btn btn-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">{{ __('Logout') }}</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.min.js"></script> <!-- Include jQuery DataTables -->
<script src="https://cdn.datatables.net/2.0.7/js/dataTables.bootstrap4.min.js"></script> <!-- Include DataTables Bootstrap 4 integration -->
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Your custom scripts -->
<script>
   $(document).ready(function() {
    $('#dataTable').DataTable({
        "error": function ( settings, helpPage, message ) {
            // Handle the error silently without displaying to the user
            console.log("DataTables error:", message);
        }
    });
});

</script>



</body>
</html>
