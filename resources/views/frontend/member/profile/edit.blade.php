@extends('layouts.member')

@section('title', 'Edit Profile')

@section('member-content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 px-6 py-5 flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Edit Profile
            </h2>

            <a href="{{ route('member.profile') }}"
                class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition no-underline">
                <i class="fa-solid fa-arrow-left text-sm"></i>
                Kembali
            </a>
        </div>

        {{-- FORM CARD: PROFILE --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            {{-- TOP SECTION --}}
            <div class="bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600 px-8 py-8 text-white">
                <div class="flex flex-col sm:flex-row items-center sm:items-center gap-6">
                    {{-- Profile Image --}}
                    <div class="flex-shrink-0">
                        <img id="previewImage" src="{{ auth()->user()->profileImage() }}"
                            class="w-24 h-24 rounded-full border-4 border-white object-cover shadow-lg">
                    </div>

                    {{-- Text & Actions --}}
                    <div class="flex flex-col text-center sm:text-left gap-3">
                        <p class="font-semibold text-white text-sm tracking-wide">
                            Foto Profile
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-center text-sm">
                            {{-- Change Photo --}}
                            <label class="cursor-pointer text-white/90 hover:text-white transition flex items-center gap-2">
                                <i class="fa-solid fa-camera"></i>
                                Ganti Foto
                                <input type="file" name="image" accept="image/*" class="hidden"
                                    form="updateProfileForm" onchange="previewFile(event)">
                            </label>

                            {{-- Delete Photo --}}
                            @if (auth()->user()->image)
                                <form action="{{ route('member.profile.delete_image') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center gap-2 text-red-200 hover:text-red-400 transition"
                                        onclick="return confirm('Yakin ingin menghapus foto profile?')">
                                        <i class="fa-solid fa-trash"></i>
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM PROFILE --}}
            <form id="updateProfileForm" action="{{ route('member.profile.update') }}" method="POST"
                enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Alert --}}
                @if (session('success'))
                    <div class="text-green-600 dark:text-green-400 font-medium">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="text-red-600 dark:text-red-400 font-medium">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Name --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Nama</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                        class="w-full mt-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        required>
                </div>

                {{-- Email --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Email</label>
                    <input type="email" value="{{ auth()->user()->email }}"
                        class="w-full mt-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 dark:text-gray-400"
                        disabled>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Nomor Handphone</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                        class="w-full mt-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
                        <a href="{{ route('member.profile') }}"
                            class="w-full sm:w-auto text-center px-6 py-2.5 rounded-xl 
                        bg-gray-100 dark:bg-gray-700 
                        text-gray-700 dark:text-gray-300 
                        font-semibold 
                        hover:bg-gray-200 dark:hover:bg-gray-600 
                        transition no-underline">
                            Batal
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto px-6 py-2.5 rounded-xl 
                        bg-blue-600 hover:bg-blue-700 
                        dark:bg-blue-500 dark:hover:bg-blue-600 
                        text-white font-semibold 
                        transition shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- FORM CARD: CHANGE PASSWORD --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">

            <form action="{{ route('member.profile.password_update') }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Heading --}}
                <h3 class="text-gray-700 dark:text-gray-300 font-semibold mb-4 text-lg">Ganti Password</h3>

                {{-- Alert --}}
                @if (session('password_success'))
                    <div class="text-green-600 dark:text-green-400 font-medium">{{ session('password_success') }}</div>
                @endif
                @if ($errors->password->any())
                    <div class="text-red-600 dark:text-red-400 font-medium">
                        <ul class="list-disc ml-5">
                            @foreach ($errors->password->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                {{-- Current Password --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Password Lama</label>
                    <input type="password" name="current_password"
                        class="w-full mt-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Masukkan password lama" required>
                </div>

                {{-- New Password --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-600 dark:text-gray-400">Password Baru</label>
                    <input type="password" name="password"
                        class="w-full mt-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Masukkan password baru" required>
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label class="text-sm text-gray-600 dark:text-gray-400">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation"
                        class="w-full mt-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Ulangi password baru" required>
                </div>

                {{-- ACTION BUTTON --}}
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl 
                    bg-blue-600 hover:bg-blue-700 
                    dark:bg-blue-500 dark:hover:bg-blue-600 
                    text-white font-semibold 
                    transition shadow-sm">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    {{-- PREVIEW IMAGE --}}
    <script>
        function previewFile(event) {
            const reader = new FileReader();
            reader.onload = function() {
                document.getElementById('previewImage').src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
