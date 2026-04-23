<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- 1. jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert WAJIB sebelum script kamu -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- 2. DataTables CORE (WAJIB) -->
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

    <!-- 4. Baru Buttons -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

    <!-- 5. Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')

    {{-- Base Stylesheets (depends on Laravel asset bundling tool) --}}
    @if (config('adminlte.enabled_laravel_mix', false))
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @else
        @switch(config('adminlte.laravel_asset_bundling', false))
            @case('mix')
                <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_css_path', 'css/app.css')) }}">
            @break

            @case('vite')
                @vite([config('adminlte.laravel_css_path', 'resources/css/app.css'), config('adminlte.laravel_js_path', 'resources/js/app.js')])
            @break

            @case('vite_js_only')
                @vite(config('adminlte.laravel_js_path', 'resources/js/app.js'))
            @break

            @default
                <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
                <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">

                @if (config('adminlte.google_fonts.allowed', true))
                    <link rel="stylesheet"
                        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
                @endif
        @endswitch
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])

    {{-- Livewire Styles --}}
    @if (config('adminlte.livewire'))
        @if (intval(app()->version()) >= 7)
            @livewireStyles
        @else
            <livewire:styles />
        @endif
    @endif

    {{-- Custom Stylesheets (post AdminLTE) --}}
    @yield('adminlte_css')
    <style>
        /* ===============================
       SIDEBAR THEME (DEFAULT DARK ELEGAN)
    ================================= */

        .main-sidebar {
            background: linear-gradient(180deg, #1e293b, #0f172a);
            border-right: none;
        }

        .brand-link {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }


        /* ===============================
       SIDEBAR NAV FIX (ANTI BUG)
    ================================= */

        .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 10px;

            height: 44px;
            padding: 0 12px;

            border-radius: 10px;
            transition: all 0.25s ease;

            color: #cbd5e1;
        }

        /* ICON */
        .nav-sidebar .nav-icon {
            width: 20px;
            text-align: center;
            flex-shrink: 0;
            color: #94a3b8;
        }

        /* TEXT */
        .nav-sidebar .nav-link p {
            margin: 0;
            flex: 1;
            white-space: nowrap;
        }

        /* CHEVRON */
        .nav-sidebar .nav-link .right {
            margin-left: auto;
            transition: transform 0.2s ease;
            color: #64748b;
        }

        /* ROTATE */
        .nav-sidebar .menu-open>.nav-link .right {
            transform: rotate(-90deg);
        }


        /* ===============================
       SIDEBAR MINI FIX
    ================================= */

        .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link {
            justify-content: center;
            padding: 0;
        }

        .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link p {
            display: none;
        }

        .sidebar-mini.sidebar-collapse .nav-sidebar .nav-icon {
            margin: 0;
        }

        .sidebar-mini.sidebar-collapse .nav-sidebar .nav-link .right {
            display: none;
        }


        /* ===============================
       HOVER EXPAND FIX
    ================================= */

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link {
            justify-content: flex-start;
            padding: 0 12px;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link p {
            display: inline;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link .right {
            display: inline-block;
        }

        .sidebar-mini.sidebar-collapse .main-sidebar:hover .nav-sidebar .nav-link {
            display: flex;
            align-items: center;
        }


        /* ===============================
       SUBMENU FIX
    ================================= */

        .nav-treeview {
            padding-left: 10px;
        }

        .nav-treeview .nav-link {
            height: 38px;
            font-size: 14px;
        }


        /* ===============================
       HOVER EFFECT (PREMIUM)
    ================================= */

        /* HOVER LEBIH HALUS (NO SHIFT TEXT) */
        .nav-sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        /* ICON ANIMASI SAJA */
        .nav-sidebar .nav-link .nav-icon {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .nav-sidebar .nav-link:hover .nav-icon {
            transform: translateX(3px);
            color: #38bdf8;
        }

        .nav-sidebar .nav-link:hover .nav-icon {
            color: #38bdf8;
        }

        /* ACTIVE */
        .nav-sidebar .nav-link.active {
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .nav-sidebar .nav-link.active .nav-icon {
            color: #fff;
        }


        /* ===============================
       BUG FIX TAMBAHAN
    ================================= */

        .nav-sidebar .nav-item {
            width: 100%;
        }

        .nav-sidebar .nav-link>* {
            display: inline-flex;
            align-items: center;
        }

        .main-sidebar {
            overflow-x: hidden;
        }

        /* navbar jangan turun */
        .navbar-nav {
            flex-wrap: nowrap !important;
        }

        /* link jangan pecah */
        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        /* mobile: sembunyikan text */
        @media (max-width: 576px) {
            .nav-text {
                display: none;
            }
        }

        /* navbar tetap satu baris */
        .navbar-nav {
            flex-wrap: nowrap !important;
        }

        /* text tidak turun */
        .navbar-nav .nav-link {
            white-space: nowrap;
        }

        /* MOBILE */
        @media (max-width: 576px) {

            /* sembunyikan tulisan logout */
            .nav-text {
                display: none;
            }

            /* rapihin icon */
            .navbar-nav .nav-link {
                padding: 6px 8px;
            }
        }

        /* =========================
   GLOBAL FIX (FINAL)
========================= */

        html,
        body {
            height: 100%;
            margin: 0;
            /* 🔥 WAJIB */
        }

        /* layout utama */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* wrapper ikut full */
        .wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* content dorong footer */
        .content-wrapper {
            flex: 1;
        }

        /* footer nempel bawah */
        .custom-footer {
            margin-top: auto;
            /* 🔥 KUNCI UTAMA */
            background: #ffffff;
            color: #1e293b;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .footer-link {
            color: #1e293b;
        }


        /* =========================
   DARK MODE FIX TOTAL
========================= */
        /* wrapper & content harus sama warna */
        body.dark-mode .wrapper,
        body.dark-mode .content-wrapper {
            background: #020617 !important;
        }


        /* =========================
   📦 CUSTOM FOOTER DARK MODE (MATCH SIDEBAR)
========================= */

        body.dark-mode .custom-footer {
            background: linear-gradient(180deg, #1f2937, #111827);
            color: #d1d5db;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            padding: 20px 0;
        }

        /* brand (copyright) */
        body.dark-mode .custom-footer .footer-brand {
            color: #e5e7eb;
        }



        body.dark-mode .custom-footer .footer-link:hover {
            color: #38bdf8;
        }

        /* teks "all rights reserved" */
        body.dark-mode .custom-footer .footer-text {
            color: #9ca3af;
        }




        /* ===============================
   🌙 GLOBAL DARK MODE
================================= */


        /* NAVBAR */
        body.dark-mode .main-header {
            background: #020617;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .main-header .nav-link {
            color: #cbd5e1 !important;
        }

        body.dark-mode .main-header .nav-link:hover {
            color: #fff !important;
        }

        /* CARD */
        body.dark-mode .card {
            background: #1e293b;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        }

        body.dark-mode .card-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .card-title {
            color: #e2e8f0;
        }

        /* TABLE */
        body.dark-mode table {
            color: #e2e8f0;
        }

        body.dark-mode .table thead {
            background: #020617;
        }

        body.dark-mode .table tbody tr {
            border-color: rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .table-hover tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        /* FORM */
        body.dark-mode .form-control {
            background: #020617;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        body.dark-mode .form-control:focus {
            background: #020617;
            color: #fff;
            border-color: #3b82f6;
            box-shadow: none;
        }

        /* SELECT2 */
        body.dark-mode .select2-container--default .select2-selection--single {
            background: #020617;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }

        /* BUTTON */
        body.dark-mode .btn-light {
            background: #1e293b;
            color: #e2e8f0;
            border: none;
        }

        body.dark-mode .btn-light:hover {
            background: #334155;
        }

        /* MODAL */
        body.dark-mode .modal-content {
            background: #1e293b;
            color: #e2e8f0;
        }

        body.dark-mode .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        body.dark-mode .modal-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* DROPDOWN */
        body.dark-mode .dropdown-menu {
            background: #1e293b;
            border: none;
        }

        body.dark-mode .dropdown-item {
            color: #cbd5e1;
        }

        body.dark-mode .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        /* ===============================
   PAGINATION GLASS (DARK MODE)
================================= */

        body.dark-mode .page-link {
            background: rgba(255, 255, 255, 0.06) !important;
            /* transparan */
            color: #e5e7eb !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
        }

        /* hover lebih terang sedikit */
        body.dark-mode .page-link:hover {
            background: rgba(255, 255, 255, 0.12) !important;
            color: #ffffff !important;
            border-color: rgba(255, 255, 255, 0.2) !important;
        }

        /* active page (tetap standout) */
        body.dark-mode .page-item.active .page-link {
            background: linear-gradient(135deg, #3b82f6, #06b6d4) !important;
            color: #ffffff !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.35);
        }

        /* disabled */
        body.dark-mode .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.03) !important;
            color: rgba(255, 255, 255, 0.3) !important;
            border: 1px solid rgba(255, 255, 255, 0.05) !important;
        }

        /* FOOTER */
        body.dark-mode footer {
            background: transparent;
            color: #94a3b8;
        }

        body.dark-mode footer a {
            color: #e2e8f0;
        }

        /* ===============================
   SELECT & SELECT2 DARK
================================= */

        /* ===============================
   SELECT2 SINGLE
================================= */

        body.dark-mode .select2-container--default .select2-selection--single {
            background: #020617;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            height: 38px;
            display: flex;
            align-items: center;
        }

        body.dark-mode .select2-container--default .select2-selection__rendered {
            color: #e2e8f0;
        }

        /* dropdown */
        body.dark-mode .select2-dropdown {
            background: #1e293b;
            border: none;
        }

        /* option */
        body.dark-mode .select2-results__option {
            color: #cbd5e1;
        }

        body.dark-mode .select2-results__option--highlighted {
            background: #3b82f6 !important;
            color: #fff;
        }

        /* ===============================
   ⭐ MULTI SELECT (TAGS) - DARK MODE FIX FINAL
================================= */

        /* container box */
        body.dark-mode .select2-container--default .select2-selection--multiple {
            background: #020617 !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            min-height: 38px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }

        /* CHIP / TAG (SELECTED ITEM) */
        body.dark-mode .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background: #334155 !important;
            color: #ffffff !important;
            flex-direction: row-reverse;
            /* ⭐ INI PAKSA PUTIH */
            border: 1px solid rgba(255, 255, 255, 0.15) !important;
            padding: 4px 10px;
            border-radius: 8px;
            margin-top: 4px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        /* TEKS DI DALAM CHIP (FORCE WHITE LAGI) */
        body.dark-mode .select2-container--default .select2-selection__choice span {
            color: #ffffff !important;
        }

        /* ICON REMOVE (X) */
        body.dark-mode .select2-container--default .select2-selection__choice__remove {
            color: #ffffff !important;
            margin-right: 6px;
            opacity: 0.8;
        }

        body.dark-mode .select2-container--default .select2-selection__choice__remove:hover {
            opacity: 1;
        }

        /* INPUT TYPING AREA */
        body.dark-mode .select2-container--default .select2-search--inline .select2-search__field {
            color: #e2e8f0 !important;
        }

        /* PLACEHOLDER */
        body.dark-mode .select2-container--default .select2-selection__placeholder {
            color: #94a3b8 !important;
        }

        /* DROPDOWN OPTIONS */
        body.dark-mode .select2-dropdown {
            background: #1e293b !important;
            border: none !important;
        }

        body.dark-mode .select2-results__option {
            color: #cbd5e1 !important;
        }

        body.dark-mode .select2-results__option--highlighted {
            background: #3b82f6 !important;
            color: #fff !important;
        }

        /* =========================
                                       🌙 DARK MODE - PREMIUM GLASS TABLE UI
                                    ========================= */

        /* ================= TABLE ================= */

        body.dark-mode .table {
            background: transparent !important;
            color: #e5e7eb !important;
        }

        /* HEADER */
        body.dark-mode .table-header-gradient {
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.5), rgba(14, 165, 233, 0.4)) !important;
            backdrop-filter: blur(8px);
        }

        body.dark-mode .table-header-gradient th {
            color: #f8fafc !important;
            background: transparent !important;
            border: none !important;
        }

        /* ROW */
        body.dark-mode .table tbody tr {
            background: transparent !important;
            color: #e5e7eb !important;
        }

        /* CELL */
        body.dark-mode .table td {
            background: transparent !important;
            color: #e5e7eb !important;
            border-color: rgba(148, 163, 184, 0.12) !important;
        }



        /* ================= DATA TABLE WRAPPER ================= */

        body.dark-mode .dataTables_wrapper {
            background: transparent !important;
            color: #e5e7eb !important;
        }

        /* ================= FILTER ================= */

        body.dark-mode #filterEmployee,
        body.dark-mode #filterService,
        body.dark-mode .filter-select {
            background: rgba(15, 23, 42, 0.6) !important;
            color: #e5e7eb !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
        }

        /* ================= BUTTON ================= */

        body.dark-mode .btn-gradient-primary {
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
            color: #fff;
        }

        /* ================= TITLE ================= */

        body.dark-mode .page-title {
            color: #f1f5f9 !important;
        }

        body.dark-mode .title-divider {
            background: linear-gradient(90deg, #3b82f6, #06b6d4);
        }

        /* ================= BADGE ================= */

        body.dark-mode .bg-gradient-success,
        body.dark-mode .bg-gradient-info,
        body.dark-mode .bg-gradient-danger,
        body.dark-mode .bg-gradient-secondary {
            filter: brightness(0.9);
        }

        /* ================= PAGINATION ================= */

        body.dark-mode .paginate_button {
            background: rgba(15, 23, 42, 0.4) !important;
            border: 1px solid rgba(148, 163, 184, 0.2) !important;
            color: #e5e7eb !important;
        }

        body.dark-mode .paginate_button.current {
            background: rgba(59, 130, 246, 0.8) !important;
            color: #fff !important;
        }

        /* ================= REMOVE WHITE FLASH ================= */

        body.dark-mode table,
        body.dark-mode .table,
        body.dark-mode .dataTables_wrapper,
        body.dark-mode .card {
            transition: all 0.2s ease;
        }

        /* LABEL TEXT */
        body.dark-mode .section-title,
        body.dark-mode .filter-label,
        body.dark-mode .change-status-label,
        body.dark-mode .small.fw-semibold {
            color: #e5e7eb !important;
        }

        /* ===== FILTER INPUT ===== */
        body.dark-mode #filterDate,
        body.dark-mode #filterEmployee,
        body.dark-mode #filterService,
        body.dark-mode #dateRangePicker,
        body.dark-mode .filter-select {
            background: #0f172a !important;
            border: 1px solid #334155 !important;
            color: #e5e7eb !important;
        }

        /* ================= DARK MODE DROP ZONE ================= */
        body.dark-mode .drop-zone {
            border: 2px dashed #334155;
            background: #0f172a;
            color: #e5e7eb;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        /* hover */
        body.dark-mode .drop-zone:hover {
            background: #1e293b;
            border-color: #3b82f6;
        }

        /* drag over */
        body.dark-mode .drop-zone.dragover {
            background: rgba(59, 130, 246, 0.15);
            border-color: #3b82f6;
        }

        /* ================= DARK MODE SEARCH CARD ================= */
        body.dark-mode .search-card {
            background: linear-gradient(145deg, #1e293b, #0f172a);
            border-radius: 18px;
            padding: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            color: #e5e7eb;
        }

        /* wrapper */
        body.dark-mode .search-input-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            padding: 10px 14px;
            border-radius: 14px;
            border: 1px solid #334155;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        /* focus effect (tanpa layout shift) */
        body.dark-mode .search-input-wrapper:focus-within {
            border-color: #3b82f6;
            background: #1e293b;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        /* icon */
        body.dark-mode .search-icon {
            color: #94a3b8;
            font-size: 15px;
        }

        /* input */
        body.dark-mode .search-input {
            flex: 1;
            border: none;
            background: transparent;
            outline: none;
            font-size: 14px;
            color: #e5e7eb;
        }

        /* placeholder */
        body.dark-mode .search-input::placeholder {
            color: #64748b;
        }

        /* clear button */
        body.dark-mode .clear-btn {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-size: 13px;
            display: none;
            cursor: pointer;
            transition: color 0.2s ease, transform 0.2s ease;
        }

        body.dark-mode .clear-btn:hover {
            color: #ef4444;
            transform: scale(1.05);
        }

        /* search button */
        body.dark-mode .search-btn {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            border: none;
            color: #fff;
            padding: 9px 18px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        /* glow effect (tanpa layout shift) */
        body.dark-mode .search-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            opacity: 0;
            transition: 0.4s;
        }

        body.dark-mode .search-btn:hover::before {
            opacity: 1;
            transform: translateX(100%);
        }

        /* hover */
        body.dark-mode .search-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.25);
        }

        /* click */
        body.dark-mode .search-btn:active {
            transform: scale(0.97);
        }


        /* =========================
   🌈 PREMIUM GRADIENT CARD HEADER
   ========================= */

        /* Header default gradient */
        .card.card-primary .card-header {
            background: linear-gradient(135deg, #007bff, #00b4d8) !important;
            border: none !important;
            color: #fff !important;
            border-radius: 12px 12px 0 0;
            transition: all 0.3s ease;
        }

        /* Title biar tetap putih & stabil */
        .card.card-primary .card-title {
            color: #fff !important;
            font-weight: 600;
        }

        /* icon tombol collapse */
        .card.card-primary .btn-tool {
            color: #fff !important;
        }


        /* =========================
   🌙 DARK MODE FIX (NO SHIFT, NO COLOR BREAK)
   ========================= */

        body.dark-mode .card.card-primary {
            background-color: #1f2937 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        /* tetap gradient (jangan berubah biar konsisten premium) */
        body.dark-mode .card.card-primary .card-header {
            background: linear-gradient(135deg, #007bff, #00b4d8) !important;
            color: #fff !important;
        }

        /* text di body dark mode */
        body.dark-mode .card.card-primary .card-body,
        body.dark-mode .card.card-primary label,
        body.dark-mode .card.card-primary small,
        body.dark-mode .card.card-primary select {
            color: #e5e7eb !important;
        }

        /* select dark mode */
        body.dark-mode .form-select {
            background-color: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }

        /* input file dark mode */
        body.dark-mode input[type="file"] {
            background-color: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }



        /* =========================
   🌈 GLOBAL CARD HEADER GRADIENT (SEMUA card-light + card-primary)
   ========================= */

        .card.card-light .card-header,
        .card.card-primary .card-header {
            background: linear-gradient(135deg, #007bff, #00b4d8) !important;
            border: none !important;
            color: #fff !important;
            border-radius: 12px 12px 0 0;
            transition: all 0.3s ease;
        }

        /* judul tetap putih */
        .card .card-title {
            color: #fff !important;
            font-weight: 600;
        }

        /* tombol collapse icon */
        .card .btn-tool {
            color: #fff !important;
        }

        /* =========================
   📦 CARD BODY STABILITY (FIXED SAFE VERSION)
   ========================= */

        .card {
            border: none !important;
            border-radius: 12px !important;



            /* optional stabilizer */
            position: relative;
        }

        /* tetap aman tanpa animasi aneh */
        .card .card-body {
            transition: none !important;
        }



        /* =========================
   🌙 DARK MODE FIX (NO CHANGE SHAPE / NO SHIFT)
   ========================= */

        body.dark-mode .card {
            background-color: #1f2937 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        /* tetap gradient (konsisten premium) */
        body.dark-mode .card.card-light .card-header,
        body.dark-mode .card.card-primary .card-header {
            background: linear-gradient(135deg, #007bff, #00b4d8) !important;
            color: #fff !important;
        }

        /* teks dark mode */
        body.dark-mode .card,
        body.dark-mode .card label,
        body.dark-mode .card small {
            color: #e5e7eb !important;
        }

        /* input dark mode */
        body.dark-mode .form-control {
            background-color: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }

        /* textarea dark mode */
        body.dark-mode textarea.form-control {
            background-color: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }

        /* select dark mode (kalau ada) */
        body.dark-mode select {
            background-color: #111827 !important;
            color: #fff !important;
            border: 1px solid #374151 !important;
        }

        /* Card footer default (light mode tetap normal) */
        .card-footer {
            background-color: #fff;
            border-top: 1px solid #e9ecef;
        }

        /* Dark mode fix - bikin transparan / gelap menyatu */
        body.dark-mode .card-footer {
            background-color: transparent !important;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Button premium */
        .btn-primary {
            border-radius: 8px;
            background: linear-gradient(45deg, #007bff, #00c6ff);
            border: none;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-danger {
            border-radius: 8px;
            background: linear-gradient(45deg, #dc2626, #ff4d4d);
            border: none;
            color: #fff;
            transition: all 0.2s ease;
        }

        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .nav-pills .nav-link {
            border-radius: 8px;
            font-weight: 500;
            color: #555;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(45deg, #007bff, #00c6ff);
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
        }

        /* DARK MODE FIX */
        body.dark-mode .list-group-item {
            background-color: transparent !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            color: #e5e7eb;
        }

        /* TEXT FIX (INI YANG BIKIN KELIHATAN ABU/JELEK) */
        body.dark-mode .list-group-item .text-dark {
            color: #e5e7eb !important;
        }

        body.dark-mode .list-group-item .text-muted {
            color: #94a3b8 !important;
        }

        /* =========================
   🔍 SIDEBAR SEARCH DARK MODE FIX
========================= */

        body.dark-mode .sidebar-search-results {
            background: #111827;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        body.dark-mode .sidebar-search-results .list-group {
            background: transparent;
        }

        body.dark-mode .sidebar-search-results .list-group-item {
            background: #1e293b !important;
            color: #e2e8f0 !important;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        body.dark-mode .sidebar-search-results .list-group-item:hover {
            background: #334155 !important;
        }

        /* teks "Tidak ada hasil ditemukan" */
        body.dark-mode .sidebar-search-results .search-title,
        body.dark-mode .sidebar-search-results .search-path {
            color: #cbd5e1 !important;
        }

        /* =========================
   📂 SIDEBAR DARK MODE (LEBIH CERAH)
========================= */

        body.dark-mode .main-sidebar {
            background: linear-gradient(180deg, #1f2937, #111827);
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.25);
        }

        /* nav item */
        body.dark-mode .nav-sidebar .nav-link {
            color: #d1d5db;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        /* =========================
   🧭 HEADER DARK MODE (SAMA DENGAN SIDEBAR)
========================= */

        body.dark-mode .main-header {
            background: linear-gradient(180deg, #1f2937, #111827);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
        }

        /* navbar text/icon */
        body.dark-mode .main-header .nav-link,
        body.dark-mode .main-header .navbar-nav .nav-link {
            color: #d1d5db !important;
        }

        body.dark-mode .main-header .nav-link:hover {
            color: #ffffff !important;
        }
    </style>

    {{-- Favicon --}}
    @if (config('adminlte.use_ico_only'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
    @elseif(config('adminlte.use_full_favicon'))
        <link rel="shortcut icon" href="{{ asset('favicons/favicon.ico') }}" />
        <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('favicons/apple-icon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('favicons/apple-icon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('favicons/apple-icon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('favicons/apple-icon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('favicons/apple-icon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('favicons/apple-icon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('favicons/apple-icon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('favicons/apple-icon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicons/apple-icon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicons/favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicons/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicons/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicons/android-icon-192x192.png') }}">
        <link rel="manifest" crossorigin="use-credentials" href="{{ asset('favicons/manifest.json') }}">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage" content="{{ asset('favicon/ms-icon-144x144.png') }}">
    @endif

</head>

<body class="@yield('classes_body')" @yield('body_data')>

    {{-- Body Content --}}
    @yield('body')

    <footer class="custom-footer">
        <div class="container text-center">

            <!-- BRAND + COPYRIGHT -->
            <div class="footer-brand text-sm font-weight-bold h5 mb-1 mt-4">
                © {{ date('Y') }}
                <a href="{{ route('home') }}" class="footer-link">
                    {{ $setting->bname ?? 'Nama Website' }}
                </a>
            </div>

            <!-- ALL RIGHTS RESERVED -->
            <div class="footer-text small mt-1 mb-3">
                All rights reserved.
            </div>

        </div>
    </footer>








    {{-- Base Scripts (depends on Laravel asset bundling tool) --}}
    @if (config('adminlte.enabled_laravel_mix', false))
        <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
    @else
        @switch(config('adminlte.laravel_asset_bundling', false))
            @case('mix')
                <script src="{{ mix(config('adminlte.laravel_js_path', 'js/app.js')) }}"></script>
            @break

            @case('vite')
            @case('vite_js_only')
            @break

            @default
                <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
                <script src="{{ asset('vendor/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
                <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
        @endswitch
    @endif

    {{-- Extra Configured Plugins Scripts --}}
    @include('adminlte::plugins', ['type' => 'js'])

    {{-- Livewire Script --}}
    @if (config('adminlte.livewire'))
        @if (intval(app()->version()) >= 7)
            @livewireScripts
        @else
            <livewire:scripts />
        @endif
    @endif

    {{-- Custom Scripts --}}
    @yield('adminlte_js')

    <script>
        document.addEventListener('click', function(e) {

            const btn = e.target.closest('[data-dark-mode-toggle]');
            if (!btn) return;

            e.preventDefault();

            document.body.classList.toggle('dark-mode');

            // SIMPAN
            localStorage.setItem(
                'dark-mode',
                document.body.classList.contains('dark-mode')
            );

            // UPDATE ICON
            const icon = btn.querySelector('i');
            if (icon) {
                if (document.body.classList.contains('dark-mode')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                } else {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            }

        });

        // LOAD STATE SAAT AWAL
        document.addEventListener("DOMContentLoaded", function() {

            if (localStorage.getItem('dark-mode') === 'true') {
                document.body.classList.add('dark-mode');
            }

            // set icon awal
            const btn = document.querySelector('[data-dark-mode-toggle]');
            const icon = btn?.querySelector('i');

            if (icon) {
                if (document.body.classList.contains('dark-mode')) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            }

        });
    </script>

</body>

</html>
