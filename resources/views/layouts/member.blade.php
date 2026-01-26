    @extends('layouts.app')

    @section('content')
        <div class="min-h-screen bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="grid grid-cols-12 gap-6 items-start">

                    {{-- SIDEBAR KIRI --}}
                    <aside class="col-span-12 md:col-span-3">
                        <div class="sticky top-24 self-start bg-white rounded-2xl shadow p-5 space-y-6">

                            {{-- USER --}}
                            <div class="flex items-center gap-3">
                                <img src="{{ auth()->user()->profileImage() ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}"
                                    class="w-12 h-12 rounded-full">
                                <div>
                                    <p class="font-semibold">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                </div>
                            </div>

                            <hr class="my-2">

                            {{-- POINT USER --}}
                            <div class="bg-gradient-to-r from-indigo-500 to-blue-600 text-white rounded-xl p-4 text-center">
                                <p class="text-xs opacity-80">Point Saya</p>
                                <p class="text-2xl font-bold">{{ auth()->user()->points ?? 0 }} points</p>
                            </div>

                            {{-- MENU --}}
                            <nav class="space-y-2 text-sm mt-4">

                                <a href="{{ route('member.profile') }}"
                                    class="menu-item block px-3 py-2 rounded hover:bg-gray-100">
                                    Profile
                                </a>

                                <a href="{{ route('member.profile.edit') }}"
                                    class="menu-item block px-3 py-2 rounded hover:bg-gray-100">
                                    Edit Profile
                                </a>

                                <a href="{{ route('member.transactions.index') }}"
                                    class="menu-item block px-3 py-2 rounded hover:bg-gray-100">
                                    Transaksi
                                </a>

                                <a href="{{ route('member.coupons.index') }}"
                                    class="menu-item block px-3 py-2 rounded hover:bg-gray-100">
                                    Kode Kupon
                                </a>

                                <a href="{{ route('member.coupons.redeem') }}"
                                    class="menu-item block px-3 py-2 rounded hover:bg-gray-100">
                                    Tukar Kode Kupon
                                </a>

                            </nav>

                        </div>
                    </aside>

                    {{-- KONTEN KANAN --}}
                    <main class="col-span-12 md:col-span-9 space-y-6">
                        @yield('member-content')
                    </main>

                </div>
            </div>
        </div>
    @endsection
