<!doctype html>
<html lang="en">



<head>

    <meta charset="utf-8" />
    <title>Smkn 1 Sumenep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Themesbrand" name="author" />
    <!-- PWA  -->
    <meta name="theme-color" content="#6777ef" />
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('/manifest.json') }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('') }}assets/images/logo-smk1.png">

    <!-- DataTables -->
    <link href="{{ asset('') }}assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="{{ asset('') }}assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css"
        rel="stylesheet" />

    <!-- bootstrap-datepicker css -->
    <link href="{{ asset('') }}assets/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">

    <!-- Responsive datatable examples -->
    <link href="{{ asset('') }}assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css"
        rel="stylesheet" />
    <!-- Bootstrap Css -->
    <link href="{{ asset('') }}assets/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Icons Css -->
    <link href="{{ asset('') }}assets/css/icons.min.css" rel="stylesheet" />
    <!-- App Css-->
    <link href="{{ asset('') }}assets/css/app.min.css" rel="stylesheet" />
    <!-- App js -->
    <script src="{{ asset('') }}assets/js/plugin.js"></script>
        <!-- Tambahkan CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <!-- Toastify JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    @stack('css')


    <style>
        tr.selected {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }

        .kelas-tujuan-group {
            display: block;
        }

        #select_all_ids {
            cursor: pointer;
            transform: scale(1.2);
        }

        .checkbox_ids {
            cursor: pointer;
            transform: scale(1.2);
        }

        #selectedCount {
            font-size: 0.875rem;
            padding: 0.35em 0.65em;
        }

        #openBulkPromoteModal:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }

        .notification-item.unread {
            background-color: rgba(70, 127, 207, 0.1);
        }

        .notification-item.unread:hover {
            background-color: rgba(70, 127, 207, 0.2);
        }
    </style>

</head>

<body data-sidebar="white">

    <div id="layout-wrapper">

        @include('layout.sidebar')

        <div class="main-content">
            <!-- Add this inside <body> -->
            <button id="pwa-install-btn"
                style="display:none; position: fixed; bottom: 20px; right: 20px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 8px; z-index: 1000;">
                Install App
            </button>
            @include('layout.Header');


            @yield('content')


        </div>
    </div>
    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Bootstrap Bundle with Popper (wajib untuk modal Bootstrap 5) -->
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}

<!-- Tambahkan JavaScript Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>


    <!-- bootstrap-datepicker js -->
    <script src="{{ asset('assets/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>

    <!-- Required datatable js -->
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Buttons examples -->
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/libs/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>

    <!-- Responsive examples -->
    <script src="{{ asset('') }}assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="{{ asset('') }}assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

    <!-- Datatable init js -->
    <script src="{{ asset('') }}assets/js/pages/datatables.init.js"></script>
    <!-- apexcharts -->
    <script src="{{ asset('') }}assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- dashboard init -->
    <script src="{{ asset('') }}assets/js/pages/dashboard.init.js"></script>


    <script src="{{ asset('') }}assets/js/pages/validation.init.js"></script>

    <!-- Sweet Alerts js -->
    <script src="{{ asset('') }}assets/libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Sweet alert init js-->
    <script src="{{ asset('') }}assets/js/pages/sweet-alerts.init.js"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <!-- untuk menampilkan kelas secara otomatis js -->
    <script src="{{ asset('assets/js/cdn.min.js') }}"></script>
    @stack('scripts');
    @yield('scripts');


    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if ("serviceWorker" in navigator) {
            // Register a service worker hosted at the root of the
            // site using the default scope.
            navigator.serviceWorker.register("/sw.js").then(
                (registration) => {
                    console.log("Service worker registration succeeded:", registration);
                },
                (error) => {
                    console.error(`Service worker registration failed: ${error}`);
                },
            );
        } else {
            console.error("Service workers are not supported.");
        }
    </script>
 <script src="{{ asset('pwa-install.js') }}"></script>
</body>


<!-- Mirrored from themesbrand.com/skote/layouts/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 18 Apr 2025 17:22:20 GMT -->

</html>
