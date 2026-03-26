@extends('layouts.app')

@section('title', 'Pricelist')

@section('content')
    <section
        class="relative overflow-hidden
    bg-gradient-to-b from-blue-50 to-white 
    dark:from-gray-900 dark:to-gray-800 
    py-16">

        {{-- TEXTURE LAYER --}}
        <div
            class="absolute inset-0 z-0 
        bg-[url('https://www.transparenttextures.com/patterns/foggy-birds.png')]
        opacity-100">
        </div>

        {{-- CONTENT WRAPPER --}}
        <div class="relative z-10 max-w-7xl mx-auto px-4" x-data="serviceSlider()">

            <div class="max-w-7xl mx-auto px-4" x-data="serviceSlider()">

                <!-- TITLE -->
                <div class="text-center mb-6 overflow-visible relative px-4">
                    <h2 class="font-extrabold text-center whitespace-nowrap
                bg-gradient-to-r from-blue-400 via-cyan-400 to-blue-600
                bg-clip-text text-transparent
                drop-shadow-[0_3px_10px_rgba(56,189,248,0.25)]
                font-[Playfair_Display]
                tracking-tight sm:tracking-widest
                title-glow scroll-fade"
                        style="font-size: clamp(1.3rem, 4.8vw, 3.2rem);">
                        ✦ Daftar Harga Layanan ✦
                    </h2>
                    <p
                        class="mt-3 text-center text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed scroll-fade">
                        Pilih paket foto terbaik dengan harga transparan, fleksibel, dan kualitas profesional untuk setiap
                        momen
                        spesial Anda.
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

                <!-- BUTTON GROUP (CENTERED) -->
                <div class="relative mb-10 flex justify-center">

                    <div
                        class="flex gap-3 overflow-x-auto px-3 py-3 rounded-2xl
        bg-white/80 dark:bg-gray-800/80
        backdrop-blur border border-gray-200 dark:border-gray-700
        shadow-sm
        w-fit max-w-full">

                        <!-- Semua -->
                        <button @click="selectCategory('all')"
                            :class="selected === 'all'
                                ?
                                'text-white shadow-[0_8px_24px_rgba(56,189,248,0.45)] bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600' :
                                'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            class="relative flex-shrink-0 px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap
            transition-all duration-300 ease-out
            hover:-translate-y-[1px] active:translate-y-0">

                            Semua
                        </button>

                        @foreach ($categories as $category)
                            <button @click="selectCategory('{{ $category->slug }}')"
                                :class="selected === '{{ $category->slug }}'
                                    ?
                                    'text-white shadow-[0_8px_24px_rgba(56,189,248,0.45)] bg-gradient-to-r from-blue-500 via-cyan-400 to-blue-600' :
                                    'text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600'"
                                class="relative flex-shrink-0 px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap
                transition-all duration-300 ease-out
                hover:-translate-y-[1px] active:translate-y-0">

                                {{ $category->title }}
                            </button>
                        @endforeach

                    </div>

                </div>


                <!-- SLIDER CARDS -->
                <div class="relative">
                    <!-- Arrow Buttons -->
                    <button @click="scrollLeft()"
                        class="absolute left-0 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-white dark:bg-gray-800 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button @click="scrollRight()"
                        class="absolute right-0 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-white dark:bg-gray-800 shadow hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-6 h-6 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Cards Container -->
                    <div class="overflow-x-auto scroll-smooth snap-x snap-mandatory flex gap-6 px-4 items-stretch"
                        x-ref="slider">
                        @foreach ($services as $service)
                            <template x-if="selected==='all' || selected==='{{ $service->category->slug }}'">
                                <div class="snap-center flex-shrink-0 w-[280px] sm:w-[320px] md:w-[360px] lg:w-[400px]"
                                    x-data="{ open: false }">

                                    <!-- Card -->
                                    <div
                                        class="flex flex-col rounded-2xl overflow-hidden border-2 shadow-md hover:shadow-xl transition
    {{ $service->featured ? 'bg-gradient-to-br from-blue-200 to-indigo-200 border-indigo-400' : 'bg-blue-50 dark:bg-gray-800 border-indigo-200 dark:border-indigo-700' }}">

                                        <!-- Header + Image + Price + Excerpt -->
                                        <div class="px-6 py-5 flex flex-col card-fixed-height">
                                            <div class="flex flex-col items-center text-center mb-4">
                                                <h3
                                                    class="text-[1.05rem] md:text-[1.15rem] font-semibold text-gray-900 dark:text-gray-100 tracking-wide leading-snug">
                                                    {{ $service->title }}
                                                </h3>
                                                <div
                                                    class="mt-1.5 w-10 h-[3px] rounded-full bg-gradient-to-r from-blue-500/70 to-indigo-500/70 dark:from-blue-400/70 dark:to-indigo-400/70">
                                                </div>
                                                @if ($service->featured)
                                                    <span
                                                        class="inline-block mt-2 text-xs bg-white/20 px-3 py-1 rounded-full text-gray-900 dark:text-gray-900">⭐
                                                        Favorite</span>
                                                @endif
                                            </div>

                                            <!-- Image -->
                                            <div class="w-full h-56 rounded-xl overflow-hidden mb-4">
                                                <img src="{{ $service->image ? asset('uploads/images/service/' . $service->image) : asset('uploads/images/no-image.jpg') }}"
                                                    alt="{{ $service->title }}" class="w-full h-full object-contain">
                                            </div>

                                            <!-- Price -->
                                            <div class="mt-5 flex flex-col gap-1">
                                                @if ($service->sale_price)
                                                    <span class="text-sm text-gray-400 line-through">Rp
                                                        {{ number_format($service->price, 0, ',', '.') }}</span>
                                                    <div class="flex items-end gap-2">
                                                        <span
                                                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                            Rp {{ number_format($service->sale_price, 0, ',', '.') }}</span>
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">/
                                                            {{ $service->min_people }} orang</span>
                                                    </div>
                                                @else
                                                    <div class="flex items-end gap-2">
                                                        <span
                                                            class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                                                            Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                                                        <span class="text-xs font-medium text-gray-700 dark:text-gray-300">/
                                                            {{ $service->min_people }} orang</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Excerpt -->
                                            <ul class="mt-6 space-y-3 text-sm text-gray-900 dark:text-gray-100">
                                                {!! $service->excerpt !!}</ul>
                                        </div>

                                        <!-- Detail Button -->
                                        <button @click="open = !open; $nextTick(() => syncCardHeight())"
                                            class="mt-6 mx-6 mb-6 w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white py-3 rounded-xl font-semibold transition">
                                            <span>Detail Layanan</span>
                                            <svg class="w-5 h-5 transform transition-transform"
                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        <!-- Detail Collapse -->
                                        <div x-show="open" x-collapse class="mt-0 px-6 pb-6 space-y-8">
                                            <!-- Backgrounds -->
                                            @if ($service->backgrounds->count())
                                                <div class="pt-6 border-t border-indigo-200 dark:border-indigo-700">
                                                    <h4
                                                        class="text-center font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                                        Background Tersedia</h4>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                                        @foreach ($service->backgrounds as $bg)
                                                            <div class="flex flex-col items-center gap-2">
                                                                <div
                                                                    class="w-20 h-20 rounded-xl ring-2 ring-indigo-400 overflow-hidden">
                                                                    <div class="w-full h-full"
                                                                        style="@if ($bg->type === 'color') background-color: {{ $bg->value }}; @else background-image:url('{{ asset('storage/' . $bg->value) }}'); background-size:cover; background-position:center; @endif">
                                                                    </div>
                                                                </div>
                                                                <span
                                                                    class="text-xs text-center text-gray-900 dark:text-gray-100">{{ $bg->name }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Addons -->
                                            @if ($service->addons()->count())
                                                <div class="pt-6 border-t border-indigo-200 dark:border-indigo-700">
                                                    <h4
                                                        class="text-center font-semibold mb-4 text-gray-900 dark:text-gray-100">
                                                        Layanan Tambahan
                                                    </h4>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                        @foreach ($service->addons()->orderBy('sort_order')->get() as $addon)
                                                            <div
                                                                class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow text-center">
                                                                <div class="font-semibold text-gray-900 dark:text-gray-100">
                                                                    {{ $addon->name }}</div>
                                                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                                                    Rp {{ number_format($addon->price, 0, ',', '.') }}
                                                                    @if ($addon->unit === 'minute' && $addon->max_qty)
                                                                        (maks. {{ $addon->max_qty }} menit)
                                                                    @elseif($addon->unit === 'person')
                                                                        / orang
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                    </div>


                                </div>
                            </template>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </section>

    <script>
        function serviceSlider() {
            return {
                selected: 'all',
                scrollAmount: 400,
                scrollLeft() {
                    this.$refs.slider.scrollBy({
                        left: -this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
                scrollRight() {
                    this.$refs.slider.scrollBy({
                        left: this.scrollAmount,
                        behavior: 'smooth'
                    });
                },
                selectCategory(category) {
                    this.selected = category;
                    this.$nextTick(() => syncCardHeight());
                }
            }
        }

        // Stable height sync
        function syncCardHeight() {
            const cards = document.querySelectorAll('.card-fixed-height');
            let maxHeight = 0;
            cards.forEach(card => {
                card.style.height = 'auto';
                if (card.offsetHeight > maxHeight) maxHeight = card.offsetHeight;
            });
            cards.forEach(card => card.style.height = maxHeight + 'px');
        }

        // Sync after page load
        window.addEventListener('load', () => {
            syncCardHeight();
            document.querySelectorAll('.card-fixed-height img')
                .forEach(img => img.addEventListener('load', syncCardHeight));
        });

        // Sync on window resize
        window.addEventListener('resize', syncCardHeight);
    </script>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
