@extends('layouts.app')

@section('title', 'Galeri')

@section('content')
    <section
        class="relative w-full min-h-screen py-16 
    bg-gradient-to-b from-gray-50 to-white 
    dark:from-gray-900 dark:to-gray-800
    transition-colors duration-300 overflow-hidden"
        x-data="{ selectedCategory: 'All' }">

        {{-- TEXTURE LAYER --}}
        <div
            class="absolute inset-0 z-0 
        bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]
        opacity-100">
        </div>

        {{-- Soft Glow Accent --}}
        <div class="absolute -top-32 -right-32 w-96 h-96 
        bg-blue-400/10 rounded-full blur-3xl z-0">
        </div>

        {{-- CONTENT WRAPPER --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4">

            <div class="max-w-7xl mx-auto px-4">

                <!-- TITLE -->
                <div class="text-center mb-10 overflow-visible relative px-4">
                    <h2 class="font-extrabold text-center whitespace-nowrap
        bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
        bg-clip-text text-transparent
        drop-shadow-[0_3px_10px_rgba(56,189,248,0.25)]
        font-[Playfair_Display]
        tracking-tight sm:tracking-widest
        title-glow scroll-fade"
                        style="font-size: clamp(1.3rem, 4.8vw, 3.2rem);">
                        <i class="fa-solid fa-star me-2"></i>
                        Galeri Yolo Studio
                        <i class="fa-solid fa-star ms-2"></i>
                    </h2>

                    <p
                        class="mt-3 text-center text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed scroll-fade">
                        Abadikan momen terbaikmu di Galeri YOLO Studio sekarang!
                    </p>

                    <div
                        class="mt-4 w-24 sm:w-32 md:w-44 h-[3px] mx-auto rounded-full
        bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
        shadow-[0_0_12px_rgba(56,189,248,0.4)]
        scroll-fade">
                    </div>

                    <div
                        class="absolute left-1/2 -bottom-6 -translate-x-1/2
        w-40 h-16 bg-blue-400/10 blur-2xl rounded-full scroll-fade">
                    </div>
                </div>


                <!-- Button Bar Categories -->
                <div class="relative mb-10">
                    <div
                        class="flex gap-3 overflow-x-auto px-3 py-3 rounded-2xl
        bg-white/80 dark:bg-gray-800/80
        backdrop-blur border border-gray-200 dark:border-gray-700
        shadow-sm">

                        {{-- 🔥 DINAMIS DARI SERVICE --}}
                        @php
                            $categories = $services->pluck('title')->toArray();
                        @endphp

                        <!-- All -->
                        <button @click="selectedCategory = 'All'"
                            :class="selectedCategory === 'All'
                                ?
                                'text-white shadow-[0_8px_24px_rgba(56,189,248,0.45)] bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600' :
                                'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="relative flex-shrink-0 px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap
            transition-all duration-300 ease-out
            hover:-translate-y-[1px] active:translate-y-0">
                            Semua
                        </button>

                        @foreach ($categories as $cat)
                            <button @click="selectedCategory = '{{ $cat }}'"
                                :class="selectedCategory === '{{ $cat }}'
                                    ?
                                    'text-white shadow-[0_8px_24px_rgba(56,189,248,0.45)] bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600' :
                                    'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="relative flex-shrink-0 px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap
                transition-all duration-300 ease-out
                hover:-translate-y-[1px] active:translate-y-0">
                                {{ $cat }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Fade edges -->
                    <div
                        class="pointer-events-none absolute top-0 left-0 h-full w-8
        bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent">
                    </div>
                    <div
                        class="pointer-events-none absolute top-0 right-0 h-full w-8
        bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent">
                    </div>
                </div>


                <!-- Gallery Grid -->
                @if ($galleries->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($galleries as $index => $gallery)
                            <div x-show="selectedCategory === 'All' || selectedCategory === '{{ $gallery->service->title ?? '' }}'"
                                x-transition:enter="transition ease-out duration-700"
                                x-transition:enter-start="opacity-0 translate-y-6"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                style="transition-delay: {{ $index * 80 }}ms"
                                class="group overflow-hidden rounded-2xl
                bg-white dark:bg-gray-800
                shadow-md hover:shadow-2xl
                transition-all duration-500 cursor-pointer">

                                <!-- Image Wrapper -->
                                <div class="relative w-full aspect-[4/5] overflow-hidden">

                                    <!-- BLUR BACKGROUND -->
                                    <img src="{{ asset('uploads/gallery/' . $gallery->image) }}"
                                        class="absolute inset-0 w-full h-full object-cover scale-110 blur-2xl opacity-60">

                                    <!-- MAIN IMAGE -->
                                    <img src="{{ asset('uploads/gallery/' . $gallery->image) }}" alt="{{ $gallery->title }}"
                                        class="relative z-10 w-full h-full object-cover
               transition-transform duration-700
               group-hover:scale-105">

                                    <!-- Overlay -->
                                    <div
                                        class="absolute inset-0 z-20 opacity-0 group-hover:opacity-100
               transition duration-500
               bg-gradient-to-t from-black/20 via-transparent to-transparent">
                                    </div>
                                </div>


                                <!-- Content -->
                                <div class="p-4 flex flex-col items-center justify-center text-center min-h-[110px]">
                                    <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 leading-snug">
                                        {{ $gallery->title }}
                                    </h3>

                                    {{-- 🔥 TAMBAHAN (SERVICE NAME) --}}
                                    <p class="text-xs text-blue-500 mt-1">
                                        {{ $gallery->service->title ?? '-' }}
                                    </p>

                                    @if ($gallery->description)
                                        <p
                                            class="mt-2 text-gray-600 dark:text-gray-300 text-sm leading-relaxed line-clamp-2 max-w-xs">
                                            {{ $gallery->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 mt-8">
                        Galeri masih kosong
                    </p>
                @endif
            </div>
        </div>
    </section>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Style tambahan -->
    <style>
        .transition {
            transition: all 0.3s ease;
        }
    </style>
@endsection
