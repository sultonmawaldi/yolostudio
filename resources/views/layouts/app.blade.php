<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="min-h-screen bg-gray-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- @TODO: replace SET_YOUR_CLIENT_KEY_HERE with your client key -->
    <script type="text/javascript" src="https://app.stg.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
    <!-- Note: replace with src="https://app.midtrans.com/snap/snap.js" for Production environment -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-wOopH1HTjOtfrXWE"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css"
        integrity="sha512-10/jx2EXwxxWqCLX/hHth/vu2KY3jCF70dCQB8TSgNjbCVAC/8vai53GfMDrO2Emgwccf2pJqxct9ehpzG+MTw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />


    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- ✅ GLightbox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />

    <!-- ✅ GLightbox JS -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <!-- CDN SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.8.0/css/flag-icons.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />







    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fix glitch Alpine -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans" x-data="navbar()" x-init="init()"
    x-cloak :class="{ 'overflow-hidden': loading }">


    <!-- Loading Overlay -->
    <div x-show="loading" x-cloak
        class="fixed inset-0 z-[99999] flex flex-col items-center justify-center
           transition-opacity duration-700 ease-in-out"
        :class="{
            'opacity-100 visible': loading,
            'opacity-0 invisible': !loading,
            'bg-white text-gray-900': !document.documentElement.classList.contains('dark'),
            'bg-black text-white': document.documentElement.classList.contains('dark')
        }"
        style="background-color: rgba(0,0,0,0.92); backdrop-filter: blur(8px);">
        <!-- Logo utama tanpa O -->
        <div class="relative inline-block">
            <!-- Logo dark -->
            <img src="{{ asset('uploads/images/logo-no-o-hitam.webp') }}" alt="Logo hitam"
                class="h-60 w-auto block dark:hidden">
            <!-- Logo light -->
            <img src="{{ asset('uploads/images/logo-no-o-putih.webp') }}" alt="Logo putih"
                class="h-60 w-auto hidden dark:block">

            <!-- Huruf O pertama -->
            <img src="{{ asset('uploads/images/logo-o-hitam.webp') }}" alt="O pertama hitam"
                class="absolute animate-spin-slow object-contain dark:hidden"
                style="top: 67px; left: 45px; height: 97.5px; width: 97.5px;">
            <img src="{{ asset('uploads/images/logo-o-putih.webp') }}" alt="O pertama putih"
                class="absolute animate-spin-slow object-contain hidden dark:block"
                style="top: 65.5px; left: 44px; height: 100px; width: 100px;">

            <!-- Huruf O kedua -->
            <img src="{{ asset('uploads/images/logo-o-hitam.webp') }}" alt="O kedua hitam"
                class="absolute animate-spin-reverse object-contain dark:hidden"
                style="top: 46.5px; left: 143.5px; height: 97.5px; width: 97.5px;">
            <img src="{{ asset('uploads/images/logo-o-putih.webp') }}" alt="O kedua putih"
                class="absolute animate-spin-reverse object-contain hidden dark:block"
                style="top: 46.5px; left: 141.5px; height: 100px; width: 100px;">
        </div>
    </div>


    <style>
        @keyframes spin-slow {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spin-reverse {
            0% {
                transform: rotate(360deg);
            }

            100% {
                transform: rotate(0deg);
            }
        }

        .animate-spin-slow {
            animation: spin-slow 2s linear infinite;
            transform-origin: center center;
        }

        .animate-spin-reverse {
            animation: spin-reverse 2s linear infinite;
            transform-origin: center center;
        }
    </style>

    <!-- App Wrapper -->
    <div id="app" :class="{ 'opacity-0': loading, 'opacity-100 transition-opacity duration-500': !loading }"
        class="min-h-screen flex flex-col">


        <!-- Navbar -->
        <nav x-data="{
            darkMode: localStorage.getItem('theme') === 'dark',
            mobileMenu: false,
            toggleDarkMode() {
                this.darkMode = !this.darkMode;
                if (this.darkMode) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            }
        }" x-init="if (darkMode) document.documentElement.classList.add('dark')"
            :class="{ '-translate-y-full': hidden, 'shadow-lg': scrolled }"
            class="fixed top-0 w-full z-50 transition-transform duration-300 
           backdrop-blur-xl bg-gradient-to-r from-blue-600/50 via-blue-400/40 to-white/45
           dark:from-gray-900/80 dark:via-gray-800/60 dark:to-gray-700/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="flex h-16 items-center justify-between relative">
                    <!-- Logo -->
                    <a href="/" class="flex-shrink-0 h-16 w-16 block">
                        <img src="{{ asset('uploads/images/logohitam.webp') }}"
                            class="h-full w-full object-contain block dark:hidden">
                        <img src="{{ asset('uploads/images/logoputih.webp') }}"
                            class="h-full w-full object-contain hidden dark:block">
                    </a>


                    <!-- Desktop Menu Responsive -->
                    <div
                        class="hidden lg:flex absolute left-1/2 transform -translate-x-1/2 
            items-center space-x-4
            lg:left-20 lg:translate-x-0 xl:left-1/2 xl:translate-x-[-50%]">
                        <a href="/"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition 
        {{ request()->is('/') ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold' : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                            Home
                        </a>
                        <a href="{{ route('booking') }}"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition 
        {{ request()->routeIs('booking') ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold' : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                            Booking
                        </a>
                        <a href="/pricelist"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition 
        {{ request()->is('pricelist') ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold' : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                            Pricelist
                        </a>
                        <a href="/gallery"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition 
        {{ request()->is('gallery') ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold' : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                            Gallery
                        </a>
                        <a href="/claim-photos"
                            class="rounded-xl px-4 py-2 text-sm font-medium transition 
        {{ request()->is('claim-photos') ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold' : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                            Claim Photos
                        </a>
                    </div>


                    <!-- Right Desktop -->
                    <div class="nav-right hidden lg:flex items-center justify-end gap-1">


                        <!-- Search
                        <div class="relative" x-data="{ openSearch: false }" @click.away="openSearch = false">
                            <button @click="openSearch = !openSearch"
                                class="flex items-center justify-center w-10 h-10 text-blue-900 dark:text-gray-100
                       hover:text-blue-600 dark:hover:text-gray-300 rounded-full transition-colors">
                                <i class="fa fa-search w-4 h-4 text-center leading-none"></i>
                            </button>-->

                        <!-- Search Input
                            <div x-show="openSearch" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 mt-2.5 w-64 bg-white/80 dark:bg-gray-800/80 
                    backdrop-blur-md p-2 rounded-xl shadow-lg z-50">
                                <input type="text" placeholder="Search..."
                                    class="w-full rounded-full px-4 py-2 text-sm 
                          text-blue-900 dark:text-gray-100 placeholder-blue-400 dark:placeholder-gray-400 
                          bg-white/60 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div> -->


                        <!-- Language Dropdown (Desktop) -->
                        <div class="relative" x-data="{
                            openLang: false,
                            lang: '{{ app()->getLocale() }}'
                        }" @click.away="openLang = false">
                            <!-- Tombol utama -->
                            <button @click="openLang = !openLang"
                                class="flex items-center gap-2 bg-white/70 dark:bg-gray-800/70 rounded-md px-3 py-2 backdrop-blur-md focus:ring-2 focus:ring-blue-400 hover:ring-blue-500 transition">
                                <!-- Bendera sesuai lang -->
                                <span class="fi fi-id w-5 h-4" x-show="lang==='id'"></span>
                                <span class="fi fi-gb w-5 h-4" x-show="lang==='en'"></span>
                                <!-- Chevron -->
                                <i class="fa fa-chevron-down text-sm transition-transform duration-200"
                                    :class="{ 'rotate-180': openLang }"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="openLang" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 mt-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded-xl shadow-lg z-50 w-auto overflow-hidden">
                                <ul class="flex flex-col gap-1 p-1">
                                    <!-- Indonesia -->
                                    <li>
                                        <button @click="lang='id'; changeLang(); openLang=false;"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-blue-100/50 dark:hover:bg-gray-700 rounded-md whitespace-nowrap">
                                            <span class="fi fi-id w-5 h-4"></span> ID
                                        </button>
                                    </li>
                                    <!-- Inggris -->
                                    <li>
                                        <button @click="lang='en'; changeLang(); openLang=false;"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-blue-100/50 dark:hover:bg-gray-700 rounded-md whitespace-nowrap">
                                            <span class="fi fi-gb w-5 h-4"></span> EN
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>



                        <!-- Dark Mode -->
                        <button @click="toggleDarkMode"
                            class="flex items-center justify-center w-10 h-10 text-blue-500 dark:text-yellow-300
                   hover:text-blue-600 dark:hover:text-yellow-400 rounded-full transition-colors 
                   duration-300 drop-shadow-sm hover:drop-shadow-md">
                            <template x-if="!darkMode"><i
                                    class="fa fa-moon w-4 h-4 text-center leading-none"></i></template>
                            <template x-if="darkMode"><i
                                    class="fa fa-sun w-4 h-4 text-center leading-none"></i></template>
                        </button>




                        <!-- Auth -->
                        @auth
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button @click="open = !open"
                                    class="flex items-center text-blue-900 dark:text-gray-100 px-3 py-2 rounded-lg text-sm font-medium 
                                   hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-all duration-200">
                                    <img src="{{ Auth::user()->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                        class="h-8 w-8 rounded-full mr-2 border border-blue-200 dark:border-gray-600 shadow-sm">
                                    <span class="text-sm font-semibold tracking-tight">{{ Auth::user()->name }}</span>
                                    <i class="fa fa-chevron-down ml-2 text-xs transform transition-transform duration-300 ease-out"
                                        :class="{ 'rotate-180': open }"></i>
                                </button>

                                <!-- Dropdown -->
                                <div x-show="open" x-transition.opacity.scale.duration.200ms x-cloak
                                    class="absolute right-0 mt-2 w-60 backdrop-blur-xl bg-white/70 dark:bg-gray-900/80 
                                    rounded-xl shadow-lg py-3 z-50 border border-blue-100/40 dark:border-gray-700/50 font-[Inter]">
                                    <div class="px-4 pb-3 text-center">
                                        <img src="{{ Auth::user()->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                            class="h-12 w-12 rounded-full mb-2 border border-blue-200 dark:border-gray-600 shadow-sm mx-auto">
                                        <p class="text-[15px] font-semibold text-blue-900 dark:text-gray-100 leading-snug">
                                            {{ Auth::user()->name }}
                                        </p>
                                        <p class="text-[9px] text-blue-800/80 dark:text-gray-400 mt-0.5 truncate">
                                            {{ Auth::user()->role_uid ?? 'USER' }}
                                        </p>
                                        <div class="mt-2 border-b border-blue-300/60 dark:border-gray-700/60"></div>
                                    </div>
                                    <a href="{{ route('member.profile') }}"
                                        class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
                                        {{ request()->routeIs('member.profile')
                                            ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white'
                                            : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                                        <i class="fa fa-user mr-2 text-xs opacity-80"></i> Profile
                                    </a>
                                    <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full text-left px-4 py-2 text-sm font-medium rounded-md 
                                               text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition-all duration-150">
                                            <i class="fa fa-sign-out-alt mr-2 text-xs opacity-80"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}"
                                class="border-2 border-blue-400/80 text-blue-700 dark:text-gray-100 
          bg-white/20 dark:bg-gray-800/30 
          hover:bg-blue-500/10 dark:hover:bg-gray-700/40 
          backdrop-blur-sm 
          px-4 py-2 rounded-xl text-sm font-semibold 
          transition-all duration-300 shadow-md hover:shadow-lg">
                                {{ __('Login') }}
                            </a>
                            <a href="{{ route('register') }}"
                                class="border-2 border-blue-400/80 text-white bg-blue-500/80 
          hover:bg-blue-600/90 dark:bg-blue-600/70 dark:hover:bg-blue-700/90 
          px-4 py-2 rounded-xl text-sm font-semibold 
          transition-all duration-300 shadow-md hover:shadow-lg">
                                {{ __('Register') }}
                            </a>
                        @endauth

                    </div>

                    <!-- Mobile -->
                    <div class="lg:hidden flex items-center space-x-2">

                        <!-- Search
                        <div class="relative" x-data="{ openSearch: false }" @click.away="openSearch = false">
                            <button @click="openSearch = !openSearch"
                                class="flex items-center justify-center w-10 h-10 text-blue-900 dark:text-gray-100
                       hover:text-blue-600 dark:hover:text-blue-400 rounded-md transition-colors">
                                <i class="fa fa-search w-4 h-4 text-center leading-none text-lg"></i>
                            </button>
                            <div x-show="openSearch" x-transition.opacity.duration.200ms x-cloak
                                class="absolute right-0 mt-2.5 w-64 bg-white/80 dark:bg-gray-800/80 
                    backdrop-blur-md p-2 rounded-xl shadow-lg z-50">
                                <input type="text" placeholder="Search..."
                                    class="w-full rounded-full px-4 py-2 text-sm 
                          text-blue-900 dark:text-gray-100 placeholder-blue-400 dark:placeholder-gray-400 
                          bg-white/60 dark:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                            </div>
                        </div>-->

                        <!-- Language Mobile Compact -->
                        <div class="relative" x-data="{ openLangMobile: false, lang: 'id' }" @click.away="openLangMobile = false">
                            <!-- Tombol utama -->
                            <button @click="openLangMobile = !openLangMobile"
                                class="inline-flex items-center gap-2 bg-white/70 dark:bg-gray-800/70 rounded px-3 py-2 text-sm font-semibold uppercase backdrop-blur-md focus:ring-2 focus:ring-blue-400 hover:ring-blue-500 transition">
                                <!-- Bendera -->
                                <span class="fi fi-id w-5 h-4" x-show="lang==='id'"></span>
                                <span class="fi fi-gb w-5 h-4" x-show="lang==='en'"></span>
                                <!-- Chevron -->
                                <i class="fa fa-chevron-down text-sm ml-1 transition-transform duration-200"
                                    :class="{ 'rotate-180': openLangMobile }"></i>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="openLangMobile" x-transition.opacity.duration.150ms x-cloak
                                class="absolute right-0 mt-1 bg-white/90 dark:bg-gray-800/90 backdrop-blur-md rounded shadow-lg z-50 w-max min-w-[60px] overflow-hidden">
                                <ul class="flex flex-col gap-1 p-1">
                                    <!-- Indonesia -->
                                    <li>
                                        <button @click="lang='id'; changeLang(); openLangMobile=false;"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-blue-100/50 dark:hover:bg-gray-700 rounded text-sm font-semibold uppercase whitespace-nowrap">
                                            <span class="fi fi-id w-5 h-4"></span> ID
                                        </button>
                                    </li>
                                    <!-- Inggris -->
                                    <li>
                                        <button @click="lang='en'; changeLang(); openLangMobile=false;"
                                            class="flex items-center gap-2 px-3 py-2 hover:bg-blue-100/50 dark:hover:bg-gray-700 rounded text-sm font-semibold uppercase whitespace-nowrap">
                                            <span class="fi fi-gb w-5 h-4"></span> EN
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>




                        <!-- Dark Mode -->
                        <button @click="toggleDarkMode"
                            class="flex items-center justify-center w-10 h-10 text-blue-500 dark:text-yellow-300
                   hover:text-blue-600 dark:hover:text-yellow-400 rounded-full transition-colors
                   duration-300 drop-shadow-sm hover:drop-shadow-md">
                            <template x-if="!darkMode"><i
                                    class="fa fa-moon w-4 h-4 text-center leading-none"></i></template>
                            <template x-if="darkMode"><i
                                    class="fa fa-sun w-4 h-4 text-center leading-none"></i></template>
                        </button>

                        <!-- Hamburger / Close -->
                        <button @click="mobileMenu = !mobileMenu"
                            class="flex items-center justify-center w-10 h-10 text-blue-900 dark:text-gray-100
                   hover:text-blue-600 dark:hover:text-gray-300 rounded-md transition-colors">
                            <template x-if="!mobileMenu"><i
                                    class="fa fa-bars w-4 h-4 text-center leading-none text-lg"></i></template>
                            <template x-if="mobileMenu"><i
                                    class="fa fa-times w-4 h-4 text-center leading-none text-lg"></i></template>
                        </button>

                    </div>

                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-cloak x-data @click.outside="mobileMenu = false">
                <!-- Overlay (tanpa blur navbar) -->
                <div @click="mobileMenu = false" x-show="mobileMenu" x-transition.opacity.duration.300ms
                    class="fixed inset-0 bg-black/40 dark:bg-black/60 z-40">
                </div>

                <!-- Slide Menu -->
                <div x-show="mobileMenu" x-transition:enter="transition ease-out duration-400"
                    x-transition:enter-start="translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0"
                    class="fixed top-16 right-0 w-72 h-[calc(100vh-4rem)] 
                bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl 
                border-l border-blue-100/40 dark:border-gray-700/50
                shadow-[0_0_25px_rgba(59,130,246,0.25)] 
                rounded-l-2xl overflow-y-auto 
                flex flex-col px-4 py-6 space-y-3 z-50 font-[Inter]">


                    <!-- Menu Links -->
                    <a href="/"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition 
                  {{ request()->is('/')
                      ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold'
                      : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                        <i class="fa fa-home mr-2 opacity-70"></i> Home
                    </a>

                    <a href="{{ route('booking') }}"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition 
                  {{ request()->routeIs('booking')
                      ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold'
                      : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                        <i class="fa fa-calendar-check mr-2 opacity-70"></i> Booking
                    </a>

                    <a href="/pricelist"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition 
                  {{ request()->is('pricelist')
                      ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold'
                      : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                        <i class="fa fa-tags mr-2 opacity-70"></i> Pricelist
                    </a>

                    <a href="/gallery"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition 
                  {{ request()->is('gallery')
                      ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold'
                      : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                        <i class="fa fa-images mr-2 opacity-70"></i> Gallery
                    </a>

                    <a href="/claim-photos"
                        class="block rounded-lg px-3 py-2 text-base font-medium transition 
                  {{ request()->is('claim-photos')
                      ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white font-semibold'
                      : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                        <i class="fa fa-envelope mr-2 opacity-70"></i> Claim Photos
                    </a>

                    <!-- Divider -->
                    <div class="border-t border-blue-200 dark:border-gray-700/80 my-3"></div>

                    <!-- Auth Section -->
                    @auth
                        <div class="space-y-1 mt-2">
                            <!-- User Info -->
                            <div
                                class="flex items-center px-3 py-2 rounded-md bg-gradient-to-r from-blue-50/40 to-blue-100/30 dark:from-gray-800/60 dark:to-gray-900/40 shadow-inner">
                                <img src="{{ Auth::user()->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}"
                                    class="h-10 w-10 rounded-full mr-3 border border-blue-200 dark:border-gray-600 shadow-sm">
                                <div class="flex flex-col text-left">
                                    <span
                                        class="text-sm font-semibold text-blue-900 dark:text-gray-100 truncate">{{ Auth::user()->name }}</span>
                                    <span
                                        class="text-[8px] text-blue-500 dark:text-gray-500 mt-0.5">{{ Auth::user()->role_uid ?? 'USER' }}</span>
                                </div>
                            </div>

                            <!-- Dashboard -->
                            <a href="{{ route('member.profile') }}"
                                class="flex items-center px-4 py-2 text-sm font-medium rounded-md transition-all duration-150
      {{ request()->routeIs('member.profile')
          ? 'bg-blue-600/10 dark:bg-gray-700 text-blue-700 dark:text-white'
          : 'text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700' }}">
                                <i class="fa fa-user mr-2 text-xs opacity-80"></i> Profile
                            </a>

                            <!-- Logout -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full text-left px-3 py-2 text-sm font-medium rounded-md 
                               text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700 transition">
                                    <i class="fa fa-sign-out-alt mr-2 text-xs opacity-80"></i> Logout
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="block px-3 py-2 text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700 rounded-md transition">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                            class="block px-3 py-2 text-blue-900 dark:text-gray-100 hover:bg-blue-600/10 dark:hover:bg-gray-700 rounded-md transition">
                            Register
                        </a>
                    @endauth
                </div>
            </div>


        </nav>



        <!-- Page Content -->
        <main class="min-h-screen pt-16">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-gray-300 mt-auto flex flex-col items-center justify-between">
            <!-- Copyright & Social Media -->
            <div class="w-full text-center py-6 border-t border-gray-700 flex flex-col items-center gap-2">
                <!-- Tahun dan brand -->
                <div class="text-base font-semibold">
                    © {{ date('Y') }} <span class="font-bold">YOLO STUDIO</span>
                </div>

                <!-- All rights reserved -->
                <div class="text-sm text-gray-400">
                    All rights reserved.
                </div>

                <!-- Social Media -->
                <div class="flex gap-4 mt-2">
                    <a href="https://www.instagram.com/yolostudio_id" target="_blank"
                        class="hover:text-pink-500 transition">
                        <i class="fab fa-instagram fa-lg"></i>
                    </a>
                    <a href="https://www.tiktok.com/@yolostudio_id" target="_blank"
                        class="hover:text-black transition">
                        <i class="fab fa-tiktok fa-lg"></i>
                    </a>
                </div>
            </div>
        </footer>


        <!-- FontAwesome (untuk ikon sosial media) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
            integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    </div>

    <script>
        function navbar() {
            return {
                hidden: false,
                scrolled: false,
                lastScroll: 0,
                mobileMenu: false,
                lang: '{{ app()->getLocale() }}', // ambil default locale dari Laravel
                darkMode: localStorage.getItem('dark') === 'true',
                loading: true,

                init() {
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    this.loading = false;

                    window.addEventListener('scroll', () => {
                        const current = window.pageYOffset;
                        this.scrolled = current > 10;

                        if (current > this.lastScroll && current > 50) {
                            this.hidden = true;
                        } else if (current < this.lastScroll) {
                            this.hidden = false;
                        }
                        this.lastScroll = current;
                    }, {
                        passive: true
                    });

                    document.querySelectorAll('a').forEach(a => {
                        a.addEventListener('click', e => {
                            const href = a.getAttribute('href');

                            // 🚫 Abaikan link GLightbox dan tombol download
                            if (a.classList.contains('glightbox') || a.classList.contains('no-loader')) {
                                return;
                            }

                            // ✅ Hanya intercept link biasa
                            if (href && !href.startsWith('#') && !href.startsWith('javascript:')) {
                                e.preventDefault();
                                this.loading = true;
                                setTimeout(() => {
                                    window.location.href = href;
                                }, 300);
                                this.mobileMenu = false;
                            }
                        });
                    });
                },

                toggleDarkMode() {
                    this.darkMode = !this.darkMode;
                    if (this.darkMode) document.documentElement.classList.add('dark');
                    else document.documentElement.classList.remove('dark');
                    localStorage.setItem('dark', this.darkMode);
                },

                changeLang() {
                    window.location.href = '/locale/' + this.lang;
                }
            }
        }
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const lightbox = GLightbox({
                selector: ".glightbox",
                touchNavigation: true,
                loop: true,
                draggable: true,
                zoomable: true,
                openEffect: "zoom",
                closeEffect: "fade",
                onOpen: () => {
                    // ✅ Jangan biarkan scrollbar hilang
                    document.documentElement.style.overflowY = "scroll";
                    document.body.style.overflow = "visible";
                },
                onClose: () => {
                    document.documentElement.style.overflowY = "";
                    document.body.style.overflow = "";
                },
            });
        });
    </script>


</body>

</html>
