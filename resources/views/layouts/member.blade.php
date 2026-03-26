@php
    $isMemberSidebar = true;
@endphp

@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300 relative">

        {{-- Overlay untuk sidebar mobile --}}
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 dark:bg-black/60 z-40 hidden lg:hidden"></div>

        <div class="max-w-7xl mx-auto px-4 py-8">

            {{-- Hamburger mobile --}}
            <div class="lg:hidden flex items-center justify-between mb-4">
                <button id="mobile-sidebar-toggle"
                    class="text-gray-700 dark:text-gray-200 focus:outline-none p-2 rounded-md hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <i id="hamburger-icon" class="bi bi-list text-2xl"></i>
                </button>
            </div>

            <div class="grid grid-cols-12 gap-6 items-start">

                <aside class="col-span-12 lg:col-span-3">

                    @php
                        $menuItems = [
                            ['route' => 'member.profile', 'icon' => 'bi-person-fill', 'label' => 'Profile'],
                            ['route' => 'member.profile.edit', 'icon' => 'bi-pencil-square', 'label' => 'Edit Profile'],
                            ['route' => 'member.transactions.index', 'icon' => 'bi-receipt', 'label' => 'Transaksi'],
                            [
                                'route' => 'member.coupons.index',
                                'icon' => 'bi-ticket-perforated-fill',
                                'label' => 'Kode Kupon',
                            ],
                            ['route' => 'member.coupons.redeem', 'icon' => 'bi-gift-fill', 'label' => 'Tukar Kupon'],
                        ];
                    @endphp

                    {{-- ================= DESKTOP SIDEBAR ================= --}}
                    <nav class="space-y-2 text-sm">
                        <div class="hidden lg:block sticky top-24">
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 space-y-6">

                                {{-- POINT SIMPLE --}}
                                <div class="flex items-center gap-3 text-sm pb-4 mb-4 relative">

                                    <div class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
                                        <i class="bi bi-star-fill text-yellow-400 text-xs"></i>
                                        <span class="tracking-wide">Point Saya</span>
                                    </div>

                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ auth()->user()->points ?? 0 }}
                                    </span>

                                    {{-- Divider --}}
                                    <div class="absolute bottom-0 left-0 w-full h-px bg-gray-300/80 dark:bg-gray-500/70">
                                    </div>
                                </div>


                                {{-- MENU --}}
                                @foreach ($menuItems as $item)
                                    <a href="{{ route($item['route']) }}"
                                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200
                       {{ request()->routeIs($item['route'])
                           ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 font-semibold'
                           : 'text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                        <i class="bi {{ $item['icon'] }} text-base"></i>
                                        <span>{{ $item['label'] }}</span>
                                    </a>
                                @endforeach

                            </div>
                        </div>
                    </nav>


                    {{-- ================= MOBILE SIDEBAR ================= --}}
                    <nav class="space-y-2 text-sm">
                        <div id="mobile-sidebar"
                            class="fixed inset-y-0 left-0 w-72 bg-white dark:bg-gray-800 shadow-xl transform -translate-x-full 
transition-transform duration-300 z-50 p-4 overflow-y-auto lg:hidden">

                            {{-- Close Button --}}
                            <div class="flex justify-between items-center mb-4">
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Menu</h2>
                                <button id="mobile-sidebar-close" class="text-gray-700 dark:text-gray-200">
                                    <i class="bi bi-x-lg text-2xl"></i>
                                </button>
                            </div>

                            {{-- USER PROFILE MOBILE --}}
                            <div class="flex items-center gap-3 mb-4">
                                <img src="{{ auth()->user()->profileImage() }}"
                                    class="h-12 w-12 rounded-full border border-blue-200 dark:border-gray-600 shadow-sm object-cover">
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ Auth::user()->name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ Auth::user()->role_uid ?? 'USER' }}
                                    </span>
                                </div>
                            </div>

                            {{-- POINT SIMPLE --}}
                            <div class="flex items-center gap-3 text-sm pb-4 mb-4 relative">

                                <div class="flex items-center gap-2 text-gray-900 dark:text-gray-100">
                                    <i class="bi bi-star-fill text-yellow-400 text-xs"></i>
                                    <span class="tracking-wide">Point Saya</span>
                                </div>

                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ auth()->user()->points ?? 0 }}
                                </span>

                                {{-- Divider --}}
                                <div class="absolute bottom-0 left-0 w-full h-px bg-gray-300/80 dark:bg-gray-500/70"></div>
                            </div>



                            {{-- MENU --}}

                            @foreach ($menuItems as $item)
                                <a href="{{ route($item['route']) }}"
                                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200
                       {{ request()->routeIs($item['route'])
                           ? 'bg-indigo-50 text-indigo-600 dark:bg-gray-700 font-semibold'
                           : 'text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    <i class="bi {{ $item['icon'] }} text-base"></i>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach


                            {{-- Garis Pemisah Lebih Tegas --}}
                            <hr class="border-0 h-px bg-gray-300 dark:bg-gray-500 my-4 opacity-80">

                            {{-- LOGOUT --}}
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="flex items-center w-full px-4 py-3 text-sm rounded-xl 
        text-gray-900 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 
        transition-all duration-200">
                                    <i class="fa-solid fa-right-from-bracket mr-3 text-base"></i>
                                    Keluar
                                </button>
                            </form>


                        </div>
                    </nav>


                </aside>


                {{-- KONTEN KANAN --}}
                <main class="col-span-12 lg:col-span-9 space-y-6">
                    @yield('member-content')
                </main>

            </div>
        </div>
    </div>

    {{-- Script untuk mobile sidebar --}}
    <script>
        const sidebarToggle = document.getElementById('mobile-sidebar-toggle');
        const sidebar = document.getElementById('mobile-sidebar');
        const sidebarClose = document.getElementById('mobile-sidebar-close');
        const overlay = document.getElementById('sidebar-overlay');
        const hamburgerIcon = document.getElementById('hamburger-icon');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            overlay.classList.remove('hidden');
            hamburgerIcon.classList.replace('bi-list', 'bi-x-lg');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            overlay.classList.add('hidden');
            hamburgerIcon.classList.replace('bi-x-lg', 'bi-list');
        }

        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
        });

        sidebarClose.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    </script>
@endsection
