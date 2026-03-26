@extends('layouts.app')

@section('title', 'Studio')

@section('content')
    <section
        class="relative w-full min-h-screen py-16 
    bg-gradient-to-b from-gray-50 to-white 
    dark:from-gray-900 dark:to-gray-800
    transition-colors duration-300 overflow-hidden">

        {{-- TEXTURE LAYER --}}
        <div
            class="absolute inset-0 z-0 
        bg-[url('https://www.transparenttextures.com/patterns/45-degree-fabric-light.png')]
        opacity-100">
        </div>

        {{-- Soft Glow Accent --}}
        <div class="absolute -top-32 -right-32 w-96 h-96 
        bg-blue-400/10 rounded-full blur-3xl z-0">
        </div>

        {{-- CONTENT WRAPPER --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mx-auto">

                {{-- TITLE STUDIO --}}
                <div class="text-center mb-10 overflow-visible relative px-4">
                    <h2 class="font-extrabold text-center whitespace-nowrap
        bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
        bg-clip-text text-transparent
        drop-shadow-[0_3px_10px_rgba(56,189,248,0.25)]
        font-[Playfair_Display]
        tracking-tight sm:tracking-widest
        title-glow scroll-fade"
                        style="font-size: clamp(1.3rem, 4.8vw, 3.2rem);">
                        ✦ Studio Kami ✦
                    </h2>

                    <p
                        class="mt-3 text-center text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed scroll-fade">
                        Temukan studio kami yang paling dekat dengan lokasi Anda dan nikmati pengalaman terbaik di YOLO
                        Studio!
                    </p>

                    <div
                        class="mt-4 w-28 sm:w-36 md:w-44 h-[3px] mx-auto rounded-full
        bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
        shadow-[0_0_12px_rgba(56,189,248,0.4)]
        scroll-fade">
                    </div>

                    <div
                        class="absolute left-1/2 -bottom-6 -translate-x-1/2
        w-40 h-16 bg-blue-400/10 blur-2xl rounded-full scroll-fade">
                    </div>
                </div>

                {{-- GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">

                    @forelse ($studios as $studio)
                        <div
                            class="group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden
                    shadow-md hover:shadow-xl transition-all duration-300 flex flex-col">

                            {{-- IMAGE --}}
                            @if ($studio->image)
                                <img src="{{ asset('uploads/studios/' . $studio->image) }}" alt="{{ $studio->name }}"
                                    class="w-full max-w-full h-auto rounded-xl
                block
                transition-transform duration-500
                group-hover:scale-105">
                            @else
                                <div
                                    class="w-full h-52 flex items-center justify-center bg-gray-200 dark:bg-gray-700 rounded-xl">
                                    <i class="fas fa-camera text-4xl text-gray-400"></i>
                                </div>
                            @endif



                            {{-- CONTENT --}}
                            <div class="p-5 flex flex-col flex-1">

                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $studio->name }}
                                </h3>

                                <div class="mt-2 space-y-2 text-sm text-gray-600 dark:text-gray-300">

                                    {{-- ADDRESS --}}
                                    <p class="flex items-start gap-2">
                                        <i class="fas fa-map-marker-alt mt-0.5 text-blue-500"></i>
                                        <span>{{ $studio->address }}</span>
                                    </p>

                                    {{-- PHONE --}}
                                    @if ($studio->phone)
                                        <p class="flex items-center gap-2">
                                            <i class="fab fa-whatsapp text-green-500"></i>
                                            <span>{{ $studio->phone }}</span>
                                        </p>
                                    @endif

                                </div>

                                @php
                                    // Normalisasi nomor WhatsApp
                                    $waNumber = preg_replace('/[^0-9]/', '', $studio->phone ?? '');

                                    // Ubah 08xxx → 62xxx
                                    if (str_starts_with($waNumber, '0')) {
                                        $waNumber = '62' . substr($waNumber, 1);
                                    }

                                    // Pesan WhatsApp
                                    $waText = urlencode("Halo, saya ingin bertanya tentang {$studio->name}.");
                                @endphp

                                <div class="mt-auto pt-5 space-y-3">

                                    {{-- GOOGLE MAPS --}}
                                    @if ($studio->google_maps)
                                        <a href="{{ $studio->google_maps }}" target="_blank"
                                            class="inline-flex items-center justify-center gap-2 w-full
                   px-4 py-2 rounded-xl
                   border border-blue-500 text-blue-600
                   hover:bg-blue-600 hover:text-white
                   transition font-medium text-sm no-underline">
                                            <i class="fas fa-map-marked-alt"></i>
                                            Lihat di Google Maps
                                        </a>
                                    @endif

                                    {{-- WHATSAPP --}}
                                    @if ($studio->phone && $waNumber)
                                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waText }}" target="_blank"
                                            class="inline-flex items-center justify-center gap-2 w-full
                                        px-4 py-2 rounded-xl
                                        bg-green-500 text-white
                                        hover:bg-green-600
                                        transition font-medium text-sm no-underline">
                                            <i class="fab fa-whatsapp"></i>
                                            Chat via WhatsApp
                                        </a>
                                    @endif

                                </div>


                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-16">
                            <p class="text-gray-500 dark:text-gray-400">
                                Belum ada studio tersedia.
                            </p>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </section>
@endsection
